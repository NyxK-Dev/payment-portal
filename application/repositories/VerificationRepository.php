<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class VerificationRepository
{
    protected $redis;
    protected $prefix = 'email_verif:';

    public function __construct()
    {
        $host = getenv('REDIS_HOST') ?: '127.0.0.1';
        $port = getenv('REDIS_PORT') ?: 6379;

        require_once APPPATH . '../vendor/autoload.php';

        try {

            $this->redis = new \Predis\Client([
                'scheme' => 'tcp',
                'host'   => getenv('REDIS_HOST'),
                'port'   => (int)getenv('REDIS_PORT'),
                'password' => getenv('REDIS_PASSWORD'),
            ]);


            // test connection
            $this->redis->ping();


        } catch (\Exception $e) {

            $this->redis = null;

            if (function_exists('log_message')) {
                log_message(
                    'error',
                    'Predis connection failed: ' . $e->getMessage()
                );
            }
        }
    }

    public function create(array $data)
    {
        // expects user_id, code, ttl (seconds)
        $userId = $data['user_id'];
        $code = $data['code'];
        $ttl = isset($data['ttl']) ? (int)$data['ttl'] : 3600;

        $key = $this->prefix . $userId;

        if (! $this->redis) {
            if (function_exists('log_message')) {
                log_message('error', 'Redis unavailable when storing verification code for user ' . $userId);
            }
            return false;
        }

        return $this->redis->setex($key, $ttl, $code);
    }

    public function findByUserId($userId)
    {
        $key = $this->prefix . $userId;
        if (! $this->redis) return null;

        $code = $this->redis->get($key);
        if ($code === false) return null;

        $ttl = $this->redis->ttl($key);

        return (object)['user_id' => $userId, 'code' => $code, 'ttl' => $ttl];
    }

    public function findByCode($userId, $code)
    {
        $record = $this->findByUserId($userId);
        if (!$record) return null;

        return ($record->code === (string)$code) ? $record : null;
    }

    public function markVerified($userId)
    {
        // remove key and set a verified flag
        $key = $this->prefix . $userId;
        if (! $this->redis) return false;

        $this->redis->del($key);
        $this->redis->setex($this->prefix . 'verified:' . $userId, 86400, '1');

        return true;
    }

    // Rate limiting helpers
    public function incrementAttempt($userId, $ip, $windowSeconds = 3600)
    {
        if (! $this->redis) return null;

        $ipHash = substr(hash('sha256', $ip), 0, 16);
        $key = $this->prefix . 'attempts:' . $userId . ':' . $ipHash;

        $count = $this->redis->incr($key);
        if ($count === 1) {
            $this->redis->expire($key, $windowSeconds);
        }

        return $count;
    }

    public function getAttemptCount($userId, $ip)
    {
        if (! $this->redis) return null;
        $ipHash = substr(hash('sha256', $ip), 0, 16);
        $key = $this->prefix . 'attempts:' . $userId . ':' . $ipHash;

        return (int) $this->redis->get($key);
    }

    public function resetAttempts($userId, $ip)
    {
        if (! $this->redis) return false;
        $ipHash = substr(hash('sha256', $ip), 0, 16);
        $key = $this->prefix . 'attempts:' . $userId . ':' . $ipHash;

        return (bool) $this->redis->del($key);
    }

    public function canResend($userId, $cooldownSeconds = 60, $dailyLimit = 5)
    {
        if (! $this->redis) return [false, 'redis_unavailable'];

        $coolKey = $this->prefix . 'resend_cool:' . $userId;
        if ($this->redis->exists($coolKey)) {
            return [false, 'cooldown'];
        }

        $countKey = $this->prefix . 'resend_count:' . $userId;
        $count = (int) $this->redis->get($countKey);
        if ($count >= $dailyLimit) {
            return [false, 'daily_limit'];
        }

        return [true, 'ok'];
    }

    public function recordResend($userId, $cooldownSeconds = 60)
    {
        if (! $this->redis) return false;

        $coolKey = $this->prefix . 'resend_cool:' . $userId;
        $countKey = $this->prefix . 'resend_count:' . $userId;

        $this->redis->setex($coolKey, $cooldownSeconds, time());

        $count = $this->redis->incr($countKey);
        if ($count === 1) {
            // set one-day expiry
            $this->redis->expire($countKey, 86400);
        }

        return $count;
    }

    public function getRedis()
    {
        return $this->redis;
    }

    public function purgeExpired()
    {
        // Redis auto-expires keys; nothing to do here
        return true;
    }
}

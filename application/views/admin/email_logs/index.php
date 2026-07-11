<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="card shadow-sm border-0">

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-light">
                    <tr>
                        <th width="5%">#</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Sent At</th>
                        <th width="20%">Response</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (!empty($logs)): ?>

                    <?php foreach ($logs as $index => $log): ?>

                    <tr>
                        <td>
                            <?= $index + 1; ?>
                        </td>

                        <td>
                            <?php if ($log->user_id): ?>
                                <span class="badge bg-info">
                                    User #<?= $log->user_id; ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted">
                                    System
                                </span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?= html_escape($log->email_to); ?>
                        </td>

                        <td>
                            <?= html_escape($log->subject); ?>
                        </td>

                        <td>
                            <?php if ($log->status_lookup_id): ?>
                                <span class="badge bg-success">
                                    Sent
                                </span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">
                                    Pending
                                </span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?= !empty($log->sent_at)
                                ? date('d M Y H:i', strtotime($log->sent_at))
                                : '-';
                            ?>
                        </td>

                        <td>
                            <?php if (!empty($log->response)): ?>
                                <small class="text-muted">
                                    <?= html_escape($log->response); ?>
                                </small>
                            <?php else: ?>
                                <span class="text-muted">
                                    -
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-envelope-open fa-2x mb-3"></i>
                                <p class="mb-0">
                                    No email logs found.
                                </p>
                            </div>
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>
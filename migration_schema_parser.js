const fs = require('fs');
const path = require('path');
const files = fs.readdirSync(path.join(process.cwd(),'application/migrations')).filter(f => f.endsWith('.php')).sort();
let out = '';
for (const f of files) {
  const txt = fs.readFileSync(path.join(process.cwd(),'application/migrations', f), 'utf8');
  const classMatch = txt.match(/class\s+Migration_Create_([A-Za-z0-9_]+)/);
  const table = classMatch ? classMatch[1].toLowerCase() : f;
  out += `FILE: ${f}\nTABLE: ${table}\n`;
  const fieldStart = txt.indexOf("$this->dbforge->add_field([");
  if (fieldStart >= 0) {
    const rest = txt.slice(fieldStart);
    const lines = rest.split(/\r?\n/);
    let collecting = false;
    let currentField = null;
    let currentAttrs = [];
    for (const line of lines) {
      if (!collecting) {
        const m = line.match(/['\"]([A-Za-z0-9_]+)['\"]\s*=>\s*\[\s*$/);
        if (m) {
          collecting = true;
          currentField = m[1];
          currentAttrs = [];
        }
      } else {
        if (line.match(/^\s*\]/)) {
          out += `  FIELD: ${currentField}\n`;
          for (const l of currentAttrs) {
            const am = l.match(/['\"]([A-Za-z0-9_]+)['\"]\s*=>\s*([^,\n]+)/);
            if (am) out += `    ${am[1]} => ${am[2].trim()}\n`;
          }
          collecting = false;
          currentField = null;
        } else {
          currentAttrs.push(line);
        }
      }
      if (collecting && line.trim() === ']);') break;
    }
  }
  const keyRegex = /\$this->dbforge->add_key\(\s*['\"]([A-Za-z0-9_]+)['\"]\s*(?:,\s*(TRUE|FALSE))?\s*\)/g;
  let km;
  while ((km = keyRegex.exec(txt)) !== null) {
    out += `  KEY: ${km[1]}${km[2] === 'TRUE' ? ' (PK)' : ''}\n`;
  }
  out += '---\n';
}
fs.writeFileSync(path.join(process.cwd(), 'migration_schema.txt'), out, 'utf8');

const fs = require('fs');
const path = require('path');

const filePath = process.argv[2];

if (!filePath) {
    process.exit(0);
}

const absolutePath = path.resolve(filePath);

fs.readFile(absolutePath, 'utf8', (error, data) => {
    if (error) {
        process.exit(0);
    }

    const updatedContent = data.replace(/class="([^"]+)"/g, (match, classList) => {
        const classes = classList.split(/\s+/).filter(Boolean);
        const normal = [], prefixed = [];

        for (const cls of classes) {
            (cls.includes(':') ? prefixed : normal).push(cls);
        }

        const sorted = [
            ...normal.sort((a, b) => a.localeCompare(b)),
            ...prefixed.sort((a, b) => a.localeCompare(b)),
        ];

        return `class="${sorted.join(' ')}"`;
    });

    fs.writeFile(absolutePath, updatedContent, 'utf8', () => {});
});

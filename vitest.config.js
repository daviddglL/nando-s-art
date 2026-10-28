import { defineConfig } from 'vitest/config';

// Tests de JS (reducer MVI de la galería). Se ejecutan con `npm test`.
export default defineConfig({
    test: {
        include: ['tests/js/**/*.test.js'],
    },
});

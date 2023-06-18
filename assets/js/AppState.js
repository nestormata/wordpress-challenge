import { signal } from 'https://cdn.jsdelivr.net/npm/preact-htm-signals-standalone/dist/standalone.js';

export function createAppState() {
    const users = signal([]);
    const currentUser = signal(null);

    return { users, currentUser };
}
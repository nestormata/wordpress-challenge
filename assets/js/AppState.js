import { signal, createContext } from 'https://cdn.jsdelivr.net/npm/preact-htm-signals-standalone/dist/standalone.js';

function createAppState() {
    const users = signal([]);
    const currentUser = signal(null);

    return { users, currentUser };
}

export const AppState = createContext(createAppState());

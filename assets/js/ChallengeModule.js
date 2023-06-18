import { h, html, render } from 'https://cdn.jsdelivr.net/npm/preact-htm-signals-standalone/dist/standalone.js';
import { App } from './App.js';
import { AppState } from './AppState.js';

/**
 * The rective app setup.
 * This file is being loaded by WordPress.
 */

const  tpl = html.bind(h);
render(tpl`
    <AppState.Provider >
        <${App} />
    </AppState.Provider>
`, document.getElementById('challenge-app'));

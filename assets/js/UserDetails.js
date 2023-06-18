import { h, html, useContext } from 'https://cdn.jsdelivr.net/npm/preact-htm-signals-standalone/dist/standalone.js';
import { AppState } from './AppState.js';

export function UserDetails(props) {
    const state = useContext(AppState);
    const currentUser = state.currentUser.value;
    const  tpl = html.bind(h);
    if (currentUser) {
        return tpl`
            <div>
                <h3>${currentUser.name}</h3>
            </div>
        `;
    }
}
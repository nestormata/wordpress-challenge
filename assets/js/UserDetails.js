import { h, html } from 'https://cdn.jsdelivr.net/npm/preact-htm-signals-standalone/dist/standalone.js';

export function UserDetails(props) {
    //const currentUser = state.currentUser.value;
    const currentUser = props.user.value;
    const  tpl = html.bind(h);
    if (currentUser) {
        return tpl`
            <div>
                <h3>${currentUser.name}</h3>
            </div>
        `;
    }
}
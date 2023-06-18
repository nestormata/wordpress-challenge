import { h, html, useContext } from 'https://cdn.jsdelivr.net/npm/preact-htm-signals-standalone/dist/standalone.js';
import { UserList } from './UserList.js';
import { UserDetails } from './UserDetails.js';
import { AppState } from './AppState.js';

export function App(props) {
    const state = useContext(AppState);
    const  tpl = html.bind(h);
    
    async function loadUsersData() {
        const response = await fetch(challenge.ajaxurl + "?" + new URLSearchParams({
            action: "challenge_users"
        }), {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            }
        });
        return response.json();
    }
    loadUsersData().then(data => state.users.value = data);
    
    return tpl`
        <div>
            <h2>Users ${state.users.value.length}</h2>
            <${UserList} />
            <${UserDetails} />
        </div>
    `;
}
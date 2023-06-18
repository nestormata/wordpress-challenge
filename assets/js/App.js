import { h, html, useContext } from 'https://cdn.jsdelivr.net/npm/preact-htm-signals-standalone/dist/standalone.js';
import { UserList } from './UserList.js';
import { UserDetails } from './UserDetails.js';
import { AppState } from './AppState.js';

/**
 * The main app that includes the Users list and the selected user details.
 */
export function App() {
    const state = useContext(AppState);
    const  tpl = html.bind(h);
    
    /**
     * Loads all the users data doing an ajax call.
     */
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
    // Calls the initial load of users when the page loads.
    loadUsersData().then(data => state.users.value = data);
    
    return tpl`
        <div class="challenge-app-data-container">
            <${UserList} />
            <${UserDetails} />
        </div>
    `;
}
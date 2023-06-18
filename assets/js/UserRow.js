import { h, html, useContext } from 'https://cdn.jsdelivr.net/npm/preact-htm-signals-standalone/dist/standalone.js';
import { AppState } from './AppState.js';

/**
 * It shows one user row of the list of users.
 * @param {user} props The user to display in this row.
 */
export function UserRow(props) {
    const state = useContext(AppState);
    const user = props.user;
    const currentUser = state.currentUser;
    const  tpl = html.bind(h);

    /**
     * Action for the button to trigger fetching the selected user.
     */
    const setCurrentUser = () => {
        findUserData(user.id);
    };

    /**
     * Ajax call to fetch the user's information.
     * @param user_id The ID of the user to fetch.
     */
    const loadUserData = async function (user_id) {
        const response = await fetch(challenge.ajaxurl + "?" + new URLSearchParams({
            action: "challenge_user",
            nonce: challenge.nonce,
            id: user_id
        }), {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            }
        });
        return response.json();
    }

    /**
     * Calls the ajax call and sets the current user when data arives.
     * @param user_id The ID of the user to fetch.
     */
    const findUserData = function(user_id) {
        currentUser.value = null;
        loadUserData(user_id).then(data => currentUser.value = data);
    };

    return tpl`
        <tr class="${ currentUser.value && currentUser.value.id == user.id ? 'active' : ''}">
            <td><a onClick="${setCurrentUser}">${user.id}</a></td>
            <td><a onClick="${setCurrentUser}">${user.name}</a></td>
            <td><a onClick="${setCurrentUser}">${user.username}</a></td>
        </tr>
    `;
}
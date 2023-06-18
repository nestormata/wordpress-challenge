import { h, html, useContext } from 'https://cdn.jsdelivr.net/npm/preact-htm-signals-standalone/dist/standalone.js';
import { AppState } from './AppState.js';

export function UserRow(props) {
    const state = useContext(AppState);
    const user = props.user;
    const currentUser = state.currentUser;
    const  tpl = html.bind(h);

    const setCurrentUser = () => {
        findUserData(user.id);
    };

    const loadUserData = async function (user_id) {
        const response = await fetch(challenge.ajaxurl + "?" + new URLSearchParams({
            action: "challenge_user",
            id: user_id
        }), {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            }
        });
        return response.json();
    }

    const findUserData = function(user_id) {
        currentUser.value = null;
        loadUserData(user_id).then(data => currentUser.value = data);
    };

    return tpl`
        <tr>
            <td><a onClick="${setCurrentUser}">${user.id}</a></td>
            <td><a onClick="${setCurrentUser}">${user.name}</a></td>
            <td><a onClick="${setCurrentUser}">${user.username}</a></td>
        </tr>
    `;
}
import { h, html, useContext } from 'https://cdn.jsdelivr.net/npm/preact-htm-signals-standalone/dist/standalone.js';
import { UserRow } from './UserRow.js';
import { AppState } from './AppState.js';

export function UserList(props) {
    const state = useContext(AppState);
    const users = state.users;
    const  tpl = html.bind(h);

    return tpl`
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Username</th>
                </tr>
            </thead>
            <tbody>
                ${users.value.map(user => (
                    tpl`<${UserRow} key=${user.id} user="${user}" />`
                ))}
            </tbody>
        </table>
    `;
}
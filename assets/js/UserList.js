import { h, html, useContext, createContext } from 'https://cdn.jsdelivr.net/npm/preact-htm-signals-standalone/dist/standalone.js';
import { UserRow } from './UserRow.js';

const AppState = createContext();

export function UserList(props) {
    const users = props.users;
    const currentUser = props.current;
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
                    tpl`<${UserRow} key=${user.id} user="${user}" current="${currentUser}" />`
                ))}
            </tbody>
        </table>
    `;
}
import { h, html, useContext, createContext } from 'https://cdn.jsdelivr.net/npm/preact-htm-signals-standalone/dist/standalone.js';
import { UserList } from './UserList.js';
import { UserDetails } from './UserDetails.js';

export function App(props) {
    const state = props.state;
    const  tpl = html.bind(h);
    
    return tpl`
        <div>
            <h2>Users ${state.users.value.length}</h2>
            <${UserList} users="${state.users}" current="${state.currentUser}" />
            <${UserDetails} user="${state.currentUser}" />
        </div>
    `;
}
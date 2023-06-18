import { h, html, useContext } from 'https://cdn.jsdelivr.net/npm/preact-htm-signals-standalone/dist/standalone.js';
import { AppState } from './AppState.js';

const tpl = html.bind(h);

/**
 * The company information card.
 * @param {company} props The company set of data.
 */
function CompanyInformation(props) {
    const company = props.company;
    if (company) {
        return tpl`
            <div class="challenge-company-details">
                ${company.name ? (tpl`<div class="challenge-company-details_name">${company.name}</div>`) : ''}
                ${company.catchPhrase ? (tpl`<div class="challenge-company-details_phrase">${company.catchPhrase}</div>`) : ''}
                ${company.bs ? (tpl`<div class="challenge-company-details_bs">${company.bs}</div>`) : ''}
            </div>
        `;    
    }
}

/**
 * The user details component.
 * It shows the selected user information.
 */
export function UserDetails() {
    const state = useContext(AppState);
    const currentUser = state.currentUser.value;
    if (currentUser) {
        return tpl`
            <div class="challenge-user-details">
                <h3>${currentUser.name}</h3>
                <${CompanyInformation} company=${currentUser.company} />
                ${currentUser.username ? (tpl`<div><label>Username:</label> ${currentUser.username}</div>`) : ''}
                ${currentUser.email ? (tpl`<div><label>Email:</label> ${currentUser.email}</div>`) : ''}
                ${currentUser.phone ? (tpl`<div><label>Phone number:</label> ${currentUser.phone}</div>`) : ''}
                ${currentUser.website ? (tpl`<div><label>Website:</label> ${currentUser.username}</div>`) : ''}
            </div>
        `;
    }
}
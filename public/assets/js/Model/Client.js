export class Client {
    constructor(data) {
        this.id = data.id;
        this.firstName = data.firstName;
        this.lastName = data.lastName;
        this.email = data.email;
        this.phone = data.phone;
        this.address = data.address;
        this.zipCode = data.zipCode;
        this.city = data.city;
    }

    getClientInfo() {
        return `
        <tr>
            <td>${this.id}</td>
            <td>${this.lastName} ${this.firstName}</td>
            <td>
                <a class="btn btn-sm" href="/client/${this.id}">Voir</a>
            </td>
        </tr>
        `;
    }
}

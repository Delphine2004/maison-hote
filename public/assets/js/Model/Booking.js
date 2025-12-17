import { Client } from "./Client.js";
import { Room } from "./Room.js";

export class Booking {
    constructor(data) {
        this.id = data.id;
        this.startingDate = data.startingDate;
        this.endingDate = data.endingDate;
        this.totalAmount = data.totalAmount;
        this.status = data.status;
        this.creationAt = data.creationAt;
        this.client = data.client
            ? new Client(data.client)
            : new Client({
                  firstName: data.firstName,
                  lastName: data.lastName,
              });
        this.room = data.room
            ? new Room(data.room)
            : new Room({ number: data.number, name: data.name });
    }

    getBookingInfo() {
        return `
        <tr>
            <td>${this.id}</td>
            <td>${this.client.lastName} ${this.client.firstName}</td>
            <td>${this.startingDate}</td>
            <td>${this.endingDate}</td>
            <td>
                <a class="btn btn-sm" href="/booking/${this.id}">Voir</a>
            </td>
        </tr>
        `;
    }
}

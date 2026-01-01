export class Room {
    constructor(data) {
        this.id = data.room_id;
        this.number = data.number;
        this.name = data.name;
        this.description = data.description;
        this.picture = data.picture;
        this.status = data.status;
    }

    getAvailableRoom() {
        return `
        <div><h2>A faire</h2></div>
        `;
    }
}

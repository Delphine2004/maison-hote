import { renderClientResult } from "./Controller/ClientController.js";
import { renderBookingResult } from "./Controller/BookingController.js";
import { renderRoomResult } from "./Controller/RoomController.js";

document.addEventListener("DOMContentLoaded", () => {
    renderClientResult();
    renderBookingResult();
    renderRoomResult();
});

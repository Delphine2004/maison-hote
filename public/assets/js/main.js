import { renderClientResult } from "./Controller/ClientController.js";
import { renderBookingResult } from "./Controller/BookingController.js";

document.addEventListener("DOMContentLoaded", () => {
    renderClientResult();
    renderBookingResult();
});

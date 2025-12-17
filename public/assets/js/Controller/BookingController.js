import { Booking } from "../Model/Booking.js";
export function renderBookingResult() {
    // Récupération des éléments
    const searchForm = document.getElementById("search-booking-form");
    const list = document.getElementById("bookings-list");

    if (!list && !searchForm) return;

    list.innerHTML =
        '<p class="text-center">Merci de séléctionner des critéres de recherche.</p>';

    searchForm.addEventListener("submit", async function (event) {
        event.preventDefault();

        // Récupération des valeurs
        const criteria = {
            id: document.getElementById("booking-id")?.value.trim() || null,
            lastName:
                document.getElementById("last-name")?.value.trim() || null,
            status: document.getElementById("status")?.value.trim() || null,
            startingDate:
                document.getElementById("arrival-date")?.value || null,
            endingDate:
                document.getElementById("departure-date")?.value || null,
            creationAt: document.getElementById("creation-date")?.value || null,
        };

        // Construction de la query string
        const params = new URLSearchParams();
        for (const key in criteria) {
            const value = criteria[key];
            if (
                value !== null &&
                value !== undefined &&
                value !== "" &&
                value !== false
            ) {
                params.append(key, value === true ? "1" : value);
            }
        }

        const url = "/booking/api?" + params.toString();

        try {
            const response = await fetch(url);

            if (!response.ok) {
                throw new Error("Erreur réseau : " + response.status);
            }

            const data = await response.json();

            if (data.status === "success") {
                list.innerHTML = "";
                if (data.count === 0) {
                    list.innerHTML = `<p class="text-center">Aucune réservation ne correspond à la recherche.</p>`;
                    searchForm.reset();
                } else {
                    list.innerHTML = `
                            <div class="col-12 col-md-10 col-lg-8">
                                <div class="table-responsive shadow-sm border border-gold rounded">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th scope="col">Ref</th>
                                                <th scope="col">Client</th>
                                                <th scope="col">Arrivée</th>
                                                <th scope="col">Départ</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="bookings-table-body"></tbody>
                                    </table>
                              </div>
                            </div>
                    `;

                    const tableBody = document.getElementById(
                        "bookings-table-body"
                    );

                    data.bookings.forEach((bookingData) => {
                        const booking = new Booking(bookingData);
                        tableBody.innerHTML += booking.getBookingInfo();
                    });
                    searchForm.reset();
                }
            }
        } catch (error) {
            list.innerHTML = `<p class="text-center">Une erreur est survenue lors de la recherche.</p>`;
        }
    });
}

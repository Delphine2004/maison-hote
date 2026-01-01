import { Room } from "../Model/Room.js";

export function renderRoomResult() {
    // Récupération des éléments
    const searchForm = document.getElementById("search-room-form");
    const list = document.getElementById("rooms-list");

    if (!list && !searchForm) return;

    list.innerHTML =
        '<p class="text-center">Merci de séléctionner des critéres de recherche.</p>';

    searchForm.addEventListener("submit", async function (event) {
        event.preventDefault();

        // Récupération des valeurs
        const criteria = {
            start: document.getElementById("starting-date").value,
            end: document.getElementById("ending-date").value,
        };

        // Vérification
        if (!criteria.start || !criteria.end) {
            alert("Veuillez sélectionner une date de début et de fin");
            return;
        }

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

        const url = "/room/api?" + params.toString();

        try {
            const response = await fetch(url);

            if (!response.ok) {
                throw new Error("Erreur réseau : " + response.status);
            }

            const data = await response.json();

            if (data.status === "success") {
                list.innerHTML = "";
                if (data.count === 0) {
                    list.innerHTML = `<p class="text-center">Aucune chambre disponible.</p>`;
                    searchForm.reset();
                } else {
                    list.innerHTML = data.rooms.forEach((roomData) => {
                        const room = new Room(roomData);
                        return room.getAvailableRoom();
                    });
                    searchForm.reset();
                }
            }
        } catch (error) {
            list.innerHTML = `<p class="text-center">Une erreur est survenue lors de la recherche.</p>`;
        }
    });
}

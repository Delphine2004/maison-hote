import { Client } from "../Model/Client.js";
export function renderClientResult() {
    // Récupération des éléments
    const searchForm = document.getElementById("search-client-form");
    const list = document.getElementById("clients-list");

    if (!list && !searchForm) return;

    list.innerHTML =
        '<p class="text-center">Merci de séléctionner des critéres de recherche.</p>';

    searchForm.addEventListener("submit", async function (event) {
        event.preventDefault();

        // Récupération des valeurs
        const criteria = {
            id: document.getElementById("client-id")?.value.trim() || null,
            lastName:
                document.getElementById("last-name")?.value.trim() || null,
            email: document.getElementById("email")?.value.trim() || null,
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

        const url = "/client/api?" + params.toString();

        try {
            const response = await fetch(url);

            if (!response.ok) {
                throw new Error("Erreur réseau : " + response.status);
            }

            const data = await response.json();

            if (data.status === "success") {
                list.innerHTML = "";
                if (data.count === 0) {
                    list.innerHTML = `<p class="text-center">Aucun client ne correspond à la recherche.</p>`;
                    searchForm.reset();
                } else {
                    list.innerHTML = `
                    <div class="col-12 col-md-10 col-lg-8">
                        <div class="table-responsive shadow-sm border rounded">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Client</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="clients-table-body"></tbody>
                            </table>
                      </div>
                    </div>
            `;

                    const tableBody =
                        document.getElementById("clients-table-body");

                    data.clients.forEach((clientData) => {
                        const client = new Client(clientData);
                        tableBody.innerHTML += client.getClientInfo();
                    });
                    searchForm.reset();
                }
            }
        } catch (error) {
            list.innerHTML = `<p class="text-center">Une erreur est survenue lors de la recherche.</p>`;
        }
    });
}

import { request, HTTP_GET, HTTP_PATCH, HTTP_PUT } from "./request.js";

import { storage } from "./storage.js";

// Sample data
const data = {
    data: [
        {
            amount: 100000,
            currency: "IDR",
            donator: "OB Berkelas",
            is_user: false,
        },
        {
            amount: 50000,
            currency: "IDR",
            donator: "BANG JEFF",
            is_user: false,
        },
        {
            amount: 35000,
            currency: "IDR",
            donator: "aisyahf",
            is_user: true,
        },
        {
            amount: 25000,
            currency: "IDR",
            donator: "Isfa",
            is_user: false,
        },
        {
            amount: 10000,
            currency: "IDR",
            donator: "AlfiFirdaus",
            is_user: true,
        },
    ],
};

export const saweria = (() => {
    const session = storage("session");
    const renderLoading = () => {
        document.getElementById("saweria-leaderboard").innerHTML = `
        <div class="col mx-2 card-body bg-theme-${theme.isDarkMode(
            "dark",
            "light"
        )} shadow p-3 mx-0 mt-0 mb-3 rounded-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center placeholder-wave">
                <span class="placeholder bg-secondary col-4 rounded-3"></span>
                <span class="placeholder bg-secondary col-2 rounded-3"></span>
            </div>
            <hr class="text-${theme.isDarkMode("light", "dark")} my-1">
            <p class="card-text placeholder-wave">
                <span class="placeholder bg-secondary col-6 rounded-3"></span>
                <span class="placeholder bg-secondary col-5 rounded-3"></span>
                <span class="placeholder bg-secondary col-12 rounded-3"></span>
            </p>
        </div>`.repeat(pagination.getPer());
    };
    const rupiah = (number) => {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
        }).format(number);
    };
    const renderLeaderboard = async () => {
        renderLoading();
        await await request(HTTP_GET, `/api/saweria/leaderboard?range=week`)
            .token(session.get("token"))
            .then((res) => {
                if (res.code === 200) {
                    document.getElementById("saweria-leaderboard").innerHTML =
                        "";
                    res.data.forEach((element) => {
                        const color =
                            element.amount >= 100000
                                ? "blue"
                                : element.amount >= 25000
                                ? "green"
                                : element.amount >= 50000
                                ? "orange"
                                : theme.isDarkMode("light", "dark");
                        document.getElementById(
                            "saweria-leaderboard"
                        ).innerHTML += `
            <div class="col mx-2 card-body bg-${
                element.amount >= 100000
                    ? "primary"
                    : element.amount >= 25000
                    ? "success"
                    : element.amount >= 50000
                    ? "warning"
                    : "dark"
            } shadow p-3 mx-0 mt-0 mb-3 rounded-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <span class="text-${color}">${element.donator}</span>
                    <span class="text-${color}">${rupiah(element.amount)}</span>
                </div>
                <hr class="text-${color} my-1">
                <p class="card-text">
                    <span class="text-${color}">${element.currency}</span>
                </p>
            </div>`;
                    });
                    if (res.data.length === 0) {
                        document.getElementById("saweria-leaderboard").innerHTML =
                            "<div class='col mx-2 card-body bg-theme-dark shadow p-3 mx-0 mt-0 mb-3 rounded-4 text-center'>Tidak ada data</div>";
                    }
                }
            });
    };

    return {
        renderLeaderboard,
    };
})();

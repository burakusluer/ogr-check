declare var jQuery: any;
declare const ogrTakipMainData: { wp_nonce: string, users: any, ajax_url: string, days: string }

document.addEventListener("DOMContentLoaded", function () {
    renderOgrTable()

    function addOgrTableBodyRow(id: bigint, username: string, hoca: string, Okuma: string, days: object) {
        const tableRow: HTMLTableSectionElement = document.querySelector("table#ogr-takip-table tbody");
        const newRow: HTMLTableRowElement = tableRow.insertRow();
        if (!days) {
            newRow.insertAdjacentHTML("beforeend", `<td class="sira">${id}</td><td class="ogrenci">${username}</td><td colspan="hoca"></td><td class="okuma"></td>`);
            for (let i = 0; i < Number(ogrTakipMainData.days); i++) {
                newRow.insertAdjacentHTML("beforeend", `<td data-day="${i+1}" data-user-id="${id}" class="${i + 1}-day undefined day"></td>`);
            }
        }

    }

    function renderOgrTable() {
        jQuery("table#ogr-takip-table tbody tr").remove()
        for (const user of ogrTakipMainData.users) {
            addOgrTableBodyRow(user.ID, user.user_nicename, null, null, user.attendance);
        }
        jQuery("table#ogr-takip-table tbody td.undefined").on("click", ajaxTDClick.bind(this));
    }

    function ajaxTDClick(event: MouseEvent): void {
        const target=event.currentTarget;
        if (target instanceof HTMLElement) {
            if (target.classList.contains("undefined")) {
                target.classList.replace("undefined", "here");
            } else if (target.classList.contains("apsent")) {
                target.classList.replace("apsent", "here");
            } else if (target.classList.contains("here")) {
                target.classList.replace("here", "apsent");
            }
            ajaxIamHereButtonAction(event);
        }

    }

    function ajaxIamHereButtonAction(event: Event): void {
        const target=event.currentTarget;
        if (target instanceof HTMLElement) {
            if (target.classList.contains('day'))
            jQuery.ajax({
                url: ogrTakipMainData.ajax_url,
                type: "POST",
                dataType: "json",
                data: {
                    action: "ogr-here-action",
                    userId: target.dataset.userId,
                    day:target.dataset.day,
                    nonce: ogrTakipMainData.wp_nonce
                },
                success: function (response: any) {
                    console.log(response);
                }
            });
        }
    }
});

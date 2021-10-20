window.addEventListener("load", () => {
    document.querySelectorAll(".datatr").forEach((tr) => {
        tr.addEventListener("click", (e) => {
            const nowtd = e.target;
            const nowtr = nowtd.parentElement;
            nowtr.classList.toggle("is-checked");
        });
    });

    document.querySelector("#act-reload").addEventListener("click", async () => {

        // https://developer.mozilla.org/ja/docs/Web/API/Fetch_API/Using_Fetch
        const url = "https://comicschedule.cranpun-tool.ml/api/json/";
        const get = async (url = "", data = {}) => {
            const response = await fetch(url, {
                method: "GET",
                mode: "cors",
                cache: "no-cache",
                headers: {
                    "Content-Type": "application/json",
                },
                redirect: "follow",
                referrerPolicy: "no-referrer",
                // body: JSON.stringify(data)
            });
            return response.json();
        };
        const list = await get(url);
        console.log(list);
        // document.querySelector("#propmt").innerHTML = list;
    });
});

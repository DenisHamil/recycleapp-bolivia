/* Donor Store — vanilla JS controller */
(function () {
    const $ = (s, c = document) => c.querySelector(s),
        $$ = (s, c = document) => Array.from(c.querySelectorAll(s));
    const on = (el, ev, fn) => el && el.addEventListener(ev, fn);

    // Tabs
    (function () {
        const tabs = $$("[data-tab-target]"),
            panes = $$("[data-tab-content]");
        if (!tabs.length) return;
        const KEY = "ds:lastTab";
        const setActive = (id) => {
            tabs.forEach((b) =>
                b.classList.toggle("active", b.dataset.tabTarget === id)
            );
            panes.forEach(
                (p) =>
                    (p.style.display =
                        p.dataset.tabContent === id ? "" : "none")
            );
            try {
                localStorage.setItem(KEY, id);
            } catch {}
        };
        setActive(localStorage.getItem(KEY) || tabs[0].dataset.tabTarget);
        tabs.forEach((b) =>
            on(b, "click", () => setActive(b.dataset.tabTarget))
        );
    })();

    // Filtros + búsqueda (querystring)
    (function () {
        const qs = new URLSearchParams(location.search);
        const apply = () => {
            const u = new URL(location.href);
            u.search = qs.toString();
            location.assign(u.toString());
        };
        const debounce = (fn, w = 350) => {
            let t;
            return (...a) => {
                clearTimeout(t);
                t = setTimeout(() => fn(...a), w);
            };
        };

        const search = $("#ds-search"),
            btn = $("#ds-search-btn"),
            range = $("#ds-range-max"),
            sort = $("#ds-sort");
        const cats = $$(".js-filter-category");

        if (search) {
            if (qs.has("search")) search.value = qs.get("search");
            on(
                search,
                "input",
                debounce(() => {
                    const v = search.value.trim();
                    v ? qs.set("search", v) : qs.delete("search");
                }, 300)
            );
            on(search, "keydown", (e) => {
                if (e.key === "Enter") apply();
            });
            if (btn) on(btn, "click", apply);
        }
        if (cats.length) {
            cats.forEach((b) =>
                on(b, "click", () => {
                    const c = b.dataset.category || "all";
                    c === "all" ? qs.delete("category") : qs.set("category", c);
                    apply();
                })
            );
        }
        if (range) {
            const cur = qs.get("max_points");
            if (cur) range.value = cur;
            on(
                range,
                "input",
                debounce(() => qs.set("max_points", range.value), 100)
            );
            on(range, "change", apply);
        }
        if (sort) {
            const cur = qs.get("sort");
            if (cur) sort.value = cur;
            on(sort, "change", () => {
                const v = sort.value;
                v ? qs.set("sort", v) : qs.delete("sort");
                apply();
            });
        }
    })();

    // Modales util
    const modal = {
        open: (el) => {
            el?.classList.add("open");
            document.body.style.overflow = "hidden";
        },
        close: (el) => {
            el?.classList.remove("open");
            document.body.style.overflow = "";
        },
    };

    // Detalles
    (function () {
        const triggers = $$("[data-open-details]"),
            modalEl = $("#ds-details-modal");
        if (!triggers.length || !modalEl) return;
        const img = $("[data-ds=img]", modalEl),
            title = $("[data-ds=title]", modalEl),
            vendor = $("[data-ds=vendor]", modalEl),
            desc = $("[data-ds=desc]", modalEl),
            cost = $("[data-ds=cost]", modalEl),
            form = $("form.js-redeem-form", modalEl);
        $$(".js-modal-close", modalEl).forEach((x) =>
            on(x, "click", () => modal.close(modalEl))
        );
        on(modalEl, "click", (e) => {
            if (e.target === modalEl) modal.close(modalEl);
        });
        on(document, "keydown", (e) => {
            if (e.key === "Escape") modal.close(modalEl);
        });

        triggers.forEach((btn) =>
            on(btn, "click", () => {
                const d = btn.dataset;
                if (img) {
                    if (d.img) {
                        img.src = d.img;
                        img.style.display = "";
                    } else {
                        img.removeAttribute("src");
                        img.style.display = "none";
                    }
                }
                if (title) title.textContent = d.title || "Recompensa";
                if (vendor) vendor.textContent = d.vendor || "Comercio aliado";
                if (desc) desc.innerHTML = d.desc || "—";
                if (cost) cost.textContent = d.cost ? `${d.cost} pts` : "—";
                if (form && d.id && form.dataset.redeemBase) {
                    form.setAttribute(
                        "action",
                        `${form.dataset.redeemBase}/${d.id}`
                    );
                }
                modal.open(modalEl);
            })
        );
    })();

    // Confirmar canje + anti-doble submit
    (function () {
        const confirmEl = $("#ds-confirm-modal");
        if (!confirmEl) return;
        const yes = $("[data-confirm-yes]", confirmEl),
            no = $("[data-confirm-no]", confirmEl);
        let formPending = null,
            submitting = false;
        $$(".js-redeem-form").forEach((f) =>
            on(f, "submit", (e) => {
                if (submitting) return;
                e.preventDefault();
                formPending = f;
                const btn = f.querySelector('button[type="submit"]');
                const costSpan = $("#ds-confirm-cost");
                const cost =
                    btn?.dataset?.cost ||
                    btn?.textContent?.match(/\d+/)?.[0] ||
                    "";
                if (costSpan) costSpan.textContent = cost;
                modal.open(confirmEl);
            })
        );
        on(yes, "click", () => {
            if (!formPending || submitting) return;
            submitting = true;
            const btn = formPending.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.textContent = "Procesando...";
            }
            modal.close(confirmEl);
            formPending.submit();
        });
        on(no, "click", () => {
            formPending = null;
            modal.close(confirmEl);
        });
        on(confirmEl, "click", (e) => {
            if (e.target === confirmEl) modal.close(confirmEl);
        });
        on(document, "keydown", (e) => {
            if (e.key === "Escape") modal.close(confirmEl);
        });
    })();

    // Copiar códigos
    (function () {
        $$(".js-copy-code").forEach((b) =>
            on(b, "click", async () => {
                const code = b.dataset.code || b.textContent.trim();
                try {
                    await navigator.clipboard.writeText(code);
                    toast("Código copiado");
                } catch {
                    toast("No se pudo copiar");
                }
            })
        );
    })();

    // Toast mínimo
    const toast = (function () {
        let holder = $("#ds-toast-holder");
        if (!holder) {
            holder = document.createElement("div");
            holder.id = "ds-toast-holder";
            holder.style.cssText =
                "position:fixed;right:16px;bottom:16px;display:flex;flex-direction:column;gap:8px;z-index:9999";
            document.body.appendChild(holder);
        }
        return (msg = "", ms = 2200) => {
            const el = document.createElement("div");
            el.textContent = msg;
            el.style.cssText =
                "background:#111827;color:#fff;padding:10px 12px;border-radius:10px;box-shadow:0 6px 20px rgba(0,0,0,.18);font-size:13px";
            holder.appendChild(el);
            setTimeout(() => {
                el.style.transition = "opacity .25s";
                el.style.opacity = "0";
                setTimeout(() => el.remove(), 300);
            }, ms);
        };
    })();
})();

/**
 * Feature Menu Widget - frontend behavior.
 * Scoped per widget instance so multiple copies can live on one page,
 * and re-initializes correctly inside the Elementor editor preview.
 */
(function () {
  "use strict";

  function initFeatureMenu(container) {
    if (!container || container.dataset.fmwInit === "1") return;
    if (typeof gsap === "undefined") {
      console.warn("Feature Menu Widget: GSAP failed to load.");
      return;
    }
    container.dataset.fmwInit = "1";

    const items = container.querySelectorAll(".feature-item");
    const images = container.querySelectorAll(".bg-image:not(.bg-video)");
    const video = container.querySelector(".bg-video");

    let currentIndex = 0;

    const isMobile = () =>
      window.matchMedia("(max-width: 1024px)").matches ||
      "ontouchstart" in window ||
      navigator.maxTouchPoints > 0;

    function showVideo() {
      images.forEach((img) => {
        gsap.to(img, {
          opacity: 0,
          scale: 1.02,
          duration: 1.0,
          ease: "power2.out",
          overwrite: "auto",
        });
      });

      if (video) {
        video.currentTime = 0;
        video.play().catch(() => {});
        gsap.fromTo(
          video,
          { opacity: 0, scale: 1.03 },
          {
            opacity: 1,
            scale: 1,
            duration: 1.2,
            ease: "power2.out",
            overwrite: "auto",
          },
        );
      }
    }

    function switchImage(index) {
      if (video) {
        gsap.to(video, {
          opacity: 0,
          scale: 1.02,
          duration: 1.0,
          ease: "power2.out",
          overwrite: "auto",
        });
        video.pause();
      }

      images.forEach((img, i) => {
        if (i === index) {
          gsap.fromTo(
            img,
            { scale: 1.03, opacity: 0 },
            {
              opacity: 1,
              scale: 1,
              duration: 1.2,
              ease: "power2.out",
              overwrite: "auto",
            },
          );
        } else {
          gsap.to(img, {
            opacity: 0,
            scale: 1.02,
            duration: 1.0,
            ease: "power2.out",
            overwrite: "auto",
          });
        }
      });
    }

    function activateItem(targetIndex) {
      const item = items[targetIndex];
      if (!item) return;

      items.forEach((el) => {
        if (el !== item && el.classList.contains("active")) {
          el.classList.remove("active");
          gsap.to(el, {
            width: isMobile() ? "100%" : "fit-content",
            duration: 0.5,
            ease: "power3.out",
            overwrite: "auto",
          });

          const oldSubmenu = el.querySelector(".submenu-panel");
          if (oldSubmenu) {
            if (!isMobile()) {
              gsap.set(oldSubmenu, { display: "none", opacity: 0 });
            } else {
              gsap.to(oldSubmenu, {
                height: 0,
                opacity: 0,
                duration: 0.4,
                ease: "power3.inOut",
                overwrite: "auto",
                onComplete: () => gsap.set(oldSubmenu, { display: "none" }),
              });
            }
          }
        }
      });

      item.classList.add("active");

      const isLink = item.classList.contains("link-item");
      gsap.to(item, {
        width: isMobile() ? "100%" : isLink ? "fit-content" : 300,
        duration: 0.5,
        ease: "power3.out",
        overwrite: "auto",
      });

      const submenu = item.querySelector(".submenu-panel");
      const featureListEl = container.querySelector(".feature-list");
      if (submenu && featureListEl) {
        if (!isMobile()) {
          const listRect = featureListEl.getBoundingClientRect();
          const itemRect = item.getBoundingClientRect();
          submenu.style.top = listRect.top - itemRect.top + "px";
          const targetH = Math.max(listRect.height, 320);
          submenu.style.height = targetH + "px";

          const listEl = submenu.querySelector(".submenu-list");
          if (listEl) {
            listEl.scrollTop = 0;
          }

          gsap.fromTo(
            submenu,
            { display: "flex", opacity: 0, x: -10, y: 0 },
            {
              opacity: 1,
              x: 0,
              y: 0,
              duration: 0.2,
              ease: "power2.out",
              overwrite: "auto",
            },
          );

          const subItemsAnim = submenu.querySelectorAll(".submenu-item");
          if (subItemsAnim.length > 0) {
            gsap.fromTo(
              subItemsAnim,
              { opacity: 0, y: 12 },
              {
                opacity: 1,
                y: 0,
                duration: 0.35,
                stagger: 0.03,
                ease: "power2.out",
                overwrite: "auto",
                delay: 0.05,
              },
            );
          }
        } else {
          submenu.style.top = "0px";
          submenu.style.display = "block";
          submenu.style.height = "auto";
          const targetHeight = submenu.scrollHeight;

          const subItemsAnim = submenu.querySelectorAll(".submenu-item");
          gsap.set(subItemsAnim, { opacity: 1, x: 0, y: 0 });

          gsap.fromTo(
            submenu,
            { height: 0, opacity: 0 },
            {
              height: targetHeight,
              opacity: 1,
              duration: 0.4,
              ease: "power3.inOut",
              overwrite: "auto",
              onComplete: () => {
                submenu.style.height = "auto";
              },
            },
          );
        }
      }

      switchImage(targetIndex);
      currentIndex = targetIndex;
    }

    showVideo();

    items.forEach((item, index) => {
      /* DESKTOP HOVER */
      item.addEventListener("mouseenter", () => {
        if (isMobile()) return;
        if (item.classList.contains("active")) return;
        activateItem(index);
      });

      /* SUBMENU ITEM HOVER -> SWITCH IMAGE LIKE PARENT */
      const subItems = item.querySelectorAll(".submenu-item");
      subItems.forEach((subItem) => {
        subItem.addEventListener("mouseenter", () => {
          if (isMobile()) return;
          const imgIndex = subItem.dataset.imgIndex;
          if (imgIndex !== undefined) {
            switchImage(parseInt(imgIndex, 10));
          }
        });

        subItem.addEventListener("mouseleave", () => {
          if (isMobile()) return;
          switchImage(index);
        });
      });

      /* MOBILE: ACCORDION FOR SUBMENU ITEMS, NORMAL NAVIGATION FOR SINGLE LINKS */
      item.addEventListener("click", (e) => {
        if (!isMobile()) return;
        if (item.classList.contains("link-item")) return; /* let the real link navigate */
        e.preventDefault();

        if (item.classList.contains("active")) {
          item.classList.remove("active");
          gsap.to(item, {
            width: "100%",
            duration: 0.5,
            ease: "power3.out",
            overwrite: "auto",
          });

          const oldSubmenu = item.querySelector(".submenu-panel");
          if (oldSubmenu) {
            gsap.to(oldSubmenu, {
              height: 0,
              opacity: 0,
              duration: 0.4,
              ease: "power3.inOut",
              overwrite: "auto",
              onComplete: () => gsap.set(oldSubmenu, { display: "none" }),
            });
          }
        } else {
          activateItem(index);
        }
      });
    });

    /* PREV / NEXT */
    const prevBtn = container.querySelector(".nav-prev");
    const nextBtn = container.querySelector(".nav-next");

    if (prevBtn) {
      prevBtn.addEventListener("click", () => {
        currentIndex--;
        if (currentIndex < 0) currentIndex = items.length - 1;
        activateItem(currentIndex);
      });
    }

    if (nextBtn) {
      nextBtn.addEventListener("click", () => {
        currentIndex++;
        if (currentIndex >= items.length) currentIndex = 0;
        activateItem(currentIndex);
      });
    }

    /* CLOSE */
    const closeBtn = container.querySelector(".close-btn");
    if (closeBtn) {
      closeBtn.addEventListener("click", () => {
        items.forEach((item) => {
          item.classList.remove("active");
          gsap.to(item, {
            width: isMobile() ? "100%" : "fit-content",
            duration: 0.5,
            ease: "power3.out",
            overwrite: "auto",
          });

          const oldSubmenu = item.querySelector(".submenu-panel");
          if (oldSubmenu) {
            gsap.to(oldSubmenu, {
              opacity: 0,
              x: isMobile() ? 0 : -10,
              height: isMobile() ? 0 : null,
              duration: 0.25,
              ease: "power2.in",
              overwrite: "auto",
              onComplete: () => gsap.set(oldSubmenu, { display: "none" }),
            });
          }
        });

        if (!container.querySelector(".feature-item.active")) {
          showVideo();
        }
      });
    }

    /* MOUSELEAVE FROM ENTIRE MENU */
    const featureList = container.querySelector(".feature-list");
    if (featureList) {
      featureList.addEventListener("mouseleave", () => {
        if (isMobile()) return;
        items.forEach((item) => {
          if (item.classList.contains("active")) {
            item.classList.remove("active");
            gsap.to(item, {
              width: "fit-content",
              duration: 0.5,
              ease: "power3.out",
              overwrite: "auto",
            });

            const oldSubmenu = item.querySelector(".submenu-panel");
            if (oldSubmenu) {
              gsap.to(oldSubmenu, {
                opacity: 0,
                x: -10,
                duration: 0.25,
                ease: "power2.in",
                overwrite: "auto",
                onComplete: () => gsap.set(oldSubmenu, { display: "none" }),
              });
            }
          }
        });

        showVideo();
      });
    }
  }

  function initAll(root) {
    (root || document).querySelectorAll(".fmw-section").forEach(initFeatureMenu);
  }

  document.addEventListener("DOMContentLoaded", function () {
    initAll(document);
  });

  /* ELEMENTOR EDITOR PREVIEW SUPPORT */
  if (window.jQuery) {
    jQuery(window).on("elementor/frontend/init", function () {
      if (window.elementorFrontend && elementorFrontend.hooks) {
        elementorFrontend.hooks.addAction(
          "frontend/element_ready/feature_menu.default",
          function ($scope) {
            initAll($scope[0]);
          },
        );
      }
    });
  }
})();

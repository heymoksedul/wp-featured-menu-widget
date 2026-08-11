(function () {
  "use strict";

  function initAccordionFeatureMenu(container) {
    if (!container || container.dataset.afmwInit === "1") return;
    if (typeof gsap === "undefined") {
      console.warn("Accordion Feature Menu: GSAP not loaded.");
      return;
    }
    container.dataset.afmwInit = "1";

    const accordionItems = container.querySelectorAll(".afmw-accordion-item");
    const mediaItems = container.querySelectorAll(".afmw-media-item");
    let currentIndex = -1;

    function switchMedia(index) {
      mediaItems.forEach((media, i) => {
        if (i === index) {
          gsap.fromTo(
            media,
            { scale: 1.05, opacity: 0 },
            { opacity: 1, scale: 1, duration: 0.5, ease: "power2.inOut", overwrite: "auto" }
          );
          if (media.tagName.toLowerCase() === "video") {
            media.currentTime = 0;
            media.play().catch(() => {});
          }
        } else {
          gsap.to(media, {
            opacity: 0,
            scale: 1.05,
            duration: 0.5,
            ease: "power2.inOut",
            overwrite: "auto",
          });
          if (media.tagName.toLowerCase() === "video") {
            media.pause();
          }
        }
      });
    }

    function activateItem(targetIndex) {
      if (currentIndex === targetIndex) return;

      const currentItem = currentIndex >= 0 ? accordionItems[currentIndex] : null;
      const nextItem = targetIndex >= 0 ? accordionItems[targetIndex] : null;

      if (currentItem) {
        const currentContent = currentItem.querySelector(".afmw-accordion-content");
        const currentTrigger = currentItem.querySelector(".afmw-accordion-trigger");
        
        // 1. Capture true start state
        const startItemWidth = currentItem.offsetWidth;
        const startContentHeight = currentContent ? currentContent.offsetHeight : 0;
        const startContentPt = currentContent ? (parseFloat(getComputedStyle(currentContent).paddingTop) || 24) : 24;
        const startContentPb = currentContent ? (parseFloat(getComputedStyle(currentContent).paddingBottom) || 24) : 24;

        // 2. Temporarily set to CLOSED state to measure target pill width
        if (currentContent) gsap.set(currentContent, { display: "none" });
        if (currentTrigger) gsap.set(currentTrigger, { display: "flex", height: "auto", paddingTop: "", paddingBottom: "" });
        currentItem.classList.remove("active");
        
        const targetItemWidth = currentItem.offsetWidth;
        const targetTriggerHeight = currentTrigger ? (currentTrigger.offsetHeight || 48) : 48;
        const targetTriggerPt = currentTrigger ? (parseFloat(getComputedStyle(currentTrigger).paddingTop) || 12) : 12;
        const targetTriggerPb = currentTrigger ? (parseFloat(getComputedStyle(currentTrigger).paddingBottom) || 12) : 12;

        // 3. Restore to ACTIVE state before animating
        currentItem.classList.add("active");
        if (currentContent) gsap.set(currentContent, { display: "block" });
        if (currentTrigger) gsap.set(currentTrigger, { height: 0, opacity: 0, paddingTop: 0, paddingBottom: 0, overflow: "hidden" });
        
        // 4. Animate width of container
        gsap.fromTo(currentItem,
          { width: startItemWidth },
          { width: targetItemWidth, duration: 0.5, ease: "power2.inOut", clearProps: "width" }
        );
        
        // 5. Animate content shrinking
        if (currentContent) {
          gsap.set(currentContent, { whiteSpace: "nowrap" });
          gsap.fromTo(currentContent, 
            { height: startContentHeight, paddingTop: startContentPt, paddingBottom: startContentPb, opacity: 1 },
            {
              height: 0,
              opacity: 0,
              paddingTop: 0,
              paddingBottom: 0,
              duration: 0.5,
              ease: "power2.inOut",
              onComplete: () => {
                gsap.set(currentContent, { display: "none", height: "auto", paddingTop: "", paddingBottom: "", whiteSpace: "" });
                currentItem.classList.remove("active");
              },
            }
          );
        } else {
          currentItem.classList.remove("active");
        }
        
        // 6. Animate trigger expanding
        if (currentTrigger) {
          gsap.fromTo(currentTrigger, 
            { height: 0, opacity: 0, paddingTop: 0, paddingBottom: 0 },
            {
              height: targetTriggerHeight,
              opacity: 1,
              paddingTop: targetTriggerPt,
              paddingBottom: targetTriggerPb,
              duration: 0.5,
              ease: "power2.inOut",
              clearProps: "all"
            }
          );
        }
      }

      if (nextItem) {
        const nextContent = nextItem.querySelector(".afmw-accordion-content");
        const nextTrigger = nextItem.querySelector(".afmw-accordion-trigger");
        
        // 1. Capture true start state
        const startItemWidth = nextItem.offsetWidth;
        const startTriggerHeight = nextTrigger ? (nextTrigger.offsetHeight || 48) : 48;
        const startTriggerPt = nextTrigger ? (parseFloat(getComputedStyle(nextTrigger).paddingTop) || 12) : 12;
        const startTriggerPb = nextTrigger ? (parseFloat(getComputedStyle(nextTrigger).paddingBottom) || 12) : 12;

        // 2. Temporarily set to OPEN state to measure
        nextItem.classList.add("active");
        const targetItemWidth = nextItem.offsetWidth;

        if (nextContent) gsap.set(nextContent, { display: "block", height: "auto", paddingTop: "", paddingBottom: "" });
        const targetContentHeight = nextContent ? nextContent.offsetHeight : 0;
        const targetContentPt = nextContent ? (parseFloat(getComputedStyle(nextContent).paddingTop) || 24) : 24;
        const targetContentPb = nextContent ? (parseFloat(getComputedStyle(nextContent).paddingBottom) || 24) : 24;

        // 3. Animate width of container
        gsap.fromTo(nextItem,
          { width: startItemWidth },
          { width: "100%", duration: 0.5, ease: "power2.inOut", clearProps: "width" }
        );

        // 4. Animate content expanding
        if (nextContent) {
          gsap.set(nextContent, { width: targetItemWidth, whiteSpace: "normal" });
          gsap.fromTo(
            nextContent,
            { height: 0, opacity: 0, paddingTop: 0, paddingBottom: 0 },
            {
              height: targetContentHeight,
              opacity: 1,
              paddingTop: targetContentPt,
              paddingBottom: targetContentPb,
              duration: 0.5,
              ease: "power2.inOut",
              onComplete: () => {
                gsap.set(nextContent, { height: "auto", paddingTop: "", paddingBottom: "", width: "", whiteSpace: "" });
              },
            }
          );
        }
        
        // 5. Animate trigger shrinking
        if (nextTrigger) {
          gsap.fromTo(nextTrigger, 
            { height: startTriggerHeight, paddingTop: startTriggerPt, paddingBottom: startTriggerPb, opacity: 1, overflow: "hidden" },
            {
              height: 0,
              opacity: 0,
              paddingTop: 0,
              paddingBottom: 0,
              duration: 0.5,
              ease: "power2.inOut",
              onComplete: () => {
                gsap.set(nextTrigger, { display: "none" });
              }
            }
          );
        }
      }

      switchMedia(targetIndex);
      currentIndex = targetIndex;
      
      if (currentIndex >= 0) {
        container.classList.add("has-active-item");
      } else {
        container.classList.remove("has-active-item");
      }
    }

    // Initialize click events
    accordionItems.forEach((item, index) => {
      item.addEventListener("click", () => {
        if (!item.classList.contains("active")) {
          activateItem(index);
        } else {
          activateItem(-1); // Toggle off if already active
        }
      });
    });

    // Navigation buttons
    const prevBtn = container.querySelector(".afmw-nav-prev");
    const nextBtn = container.querySelector(".afmw-nav-next");

    if (prevBtn) {
      prevBtn.addEventListener("click", () => {
        if (accordionItems.length === 0) return;
        let newIndex = currentIndex - 1;
        if (newIndex < 0) newIndex = accordionItems.length - 1;
        activateItem(newIndex);
      });
    }

    if (nextBtn) {
      nextBtn.addEventListener("click", () => {
        if (accordionItems.length === 0) return;
        let newIndex = currentIndex + 1;
        if (newIndex >= accordionItems.length) newIndex = 0;
        activateItem(newIndex);
      });
    }

    // Close button
    const closeBtn = container.querySelector(".afmw-close-btn");
    if (closeBtn) {
      closeBtn.addEventListener("click", () => {
        activateItem(-1);
      });
    }
    
    // Initial state setup for video (hide all media)
    switchMedia(-1);

  }

  function initAll(root) {
    (root || document).querySelectorAll(".afmw-section").forEach(initAccordionFeatureMenu);
  }

  document.addEventListener("DOMContentLoaded", function () {
    initAll(document);
  });

  /* Elementor Preview Support */
  if (window.jQuery) {
    jQuery(window).on("elementor/frontend/init", function () {
      if (window.elementorFrontend && elementorFrontend.hooks) {
        elementorFrontend.hooks.addAction(
          "frontend/element_ready/accordion_feature_menu.default",
          function ($scope) {
            initAll($scope[0]);
          }
        );
      }
    });
  }
})();

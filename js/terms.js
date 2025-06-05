// Terms and Conditions Page JavaScript
document.addEventListener("DOMContentLoaded", () => {
  // Smooth scrolling for TOC links
  const tocLinks = document.querySelectorAll(".toc-list a")

  tocLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault()

      const targetId = this.getAttribute("href").substring(1)
      const targetElement = document.getElementById(targetId)

      if (targetElement) {
        const headerOffset = 100
        const elementPosition = targetElement.getBoundingClientRect().top
        const offsetPosition = elementPosition + window.pageYOffset - headerOffset

        window.scrollTo({
          top: offsetPosition,
          behavior: "smooth",
        })

        // Update active link
        updateActiveLink(this)
      }
    })
  })

  // Update active link based on scroll position
  function updateActiveLink(activeLink) {
    tocLinks.forEach((link) => link.classList.remove("active"))
    activeLink.classList.add("active")
  }

  // Highlight current section in TOC while scrolling
  window.addEventListener("scroll", () => {
    const sections = document.querySelectorAll(".terms-section")
    const scrollPosition = window.scrollY + 150

    sections.forEach((section) => {
      const sectionTop = section.offsetTop
      const sectionHeight = section.offsetHeight
      const sectionId = section.getAttribute("id")

      if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
        const correspondingLink = document.querySelector(`.toc-list a[href="#${sectionId}"]`)
        if (correspondingLink) {
          updateActiveLink(correspondingLink)
        }
      }
    })
  })

  // Add scroll-to-top functionality
  const scrollToTopBtn = createScrollToTopButton()
  document.body.appendChild(scrollToTopBtn)

  function createScrollToTopButton() {
    const button = document.createElement("button")
    button.innerHTML = '<i class="fas fa-arrow-up"></i>'
    button.className = "scroll-to-top"
    button.style.cssText = `
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: #1e3c72;
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
            opacity: 0;
            visibility: hidden;
            z-index: 1000;
        `

    button.addEventListener("click", () => {
      window.scrollTo({
        top: 0,
        behavior: "smooth",
      })
    })

    // Show/hide button based on scroll position
    window.addEventListener("scroll", () => {
      if (window.scrollY > 300) {
        button.style.opacity = "1"
        button.style.visibility = "visible"
      } else {
        button.style.opacity = "0"
        button.style.visibility = "hidden"
      }
    })

    return button
  }

  // Print functionality
  function addPrintButton() {
    const printBtn = document.createElement("button")
    printBtn.innerHTML = '<i class="fas fa-print"></i> Print Terms'
    printBtn.className = "print-btn"
    printBtn.style.cssText = `
            position: fixed;
            bottom: 30px;
            left: 30px;
            padding: 12px 20px;
            background: #2a5298;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
            z-index: 1000;
        `

    printBtn.addEventListener("click", () => {
      window.print()
    })

    printBtn.addEventListener("mouseenter", function () {
      this.style.transform = "translateY(-2px)"
      this.style.boxShadow = "0 6px 20px rgba(0,0,0,0.4)"
    })

    printBtn.addEventListener("mouseleave", function () {
      this.style.transform = "translateY(0)"
      this.style.boxShadow = "0 4px 15px rgba(0,0,0,0.3)"
    })

    document.body.appendChild(printBtn)
  }

  addPrintButton()

  // Add reading progress indicator
  function addProgressIndicator() {
    const progressBar = document.createElement("div")
    progressBar.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 4px;
            background: linear-gradient(90deg, #1e3c72, #2a5298);
            z-index: 9999;
            transition: width 0.3s ease;
        `

    document.body.appendChild(progressBar)

    window.addEventListener("scroll", () => {
      const scrollTop = window.scrollY
      const docHeight = document.documentElement.scrollHeight - window.innerHeight
      const scrollPercent = (scrollTop / docHeight) * 100

      progressBar.style.width = scrollPercent + "%"
    })
  }

  addProgressIndicator()

  // Add copy link functionality to section headers
  const sectionHeaders = document.querySelectorAll(".terms-section h2")

  sectionHeaders.forEach((header) => {
    header.style.cursor = "pointer"
    header.title = "Click to copy link to this section"

    header.addEventListener("click", function () {
      const sectionId = this.parentElement.getAttribute("id")
      const url = window.location.origin + window.location.pathname + "#" + sectionId

      navigator.clipboard
        .writeText(url)
        .then(() => {
          showToast("Link copied to clipboard!")
        })
        .catch(() => {
          // Fallback for older browsers
          const textArea = document.createElement("textarea")
          textArea.value = url
          document.body.appendChild(textArea)
          textArea.select()
          document.execCommand("copy")
          document.body.removeChild(textArea)
          showToast("Link copied to clipboard!")
        })
    })
  })

  // Toast notification function
  function showToast(message) {
    const toast = document.createElement("div")
    toast.textContent = message
    toast.style.cssText = `
            position: fixed;
            bottom: 100px;
            right: 30px;
            background: #28a745;
            color: white;
            padding: 12px 20px;
            border-radius: 25px;
            font-size: 14px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            z-index: 10000;
            animation: slideIn 0.3s ease;
        `

    // Add animation keyframes
    if (!document.querySelector("#toast-styles")) {
      const style = document.createElement("style")
      style.id = "toast-styles"
      style.textContent = `
                @keyframes slideIn {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes slideOut {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(100%); opacity: 0; }
                }
            `
      document.head.appendChild(style)
    }

    document.body.appendChild(toast)

    setTimeout(() => {
      toast.style.animation = "slideOut 0.3s ease"
      setTimeout(() => {
        document.body.removeChild(toast)
      }, 300)
    }, 3000)
  }

  // Add keyboard navigation
  document.addEventListener("keydown", (e) => {
    // Press 'T' to focus on table of contents
    if (e.key === "t" || e.key === "T") {
      if (!e.ctrlKey && !e.altKey && !e.metaKey) {
        const firstTocLink = document.querySelector(".toc-list a")
        if (firstTocLink) {
          firstTocLink.focus()
          e.preventDefault()
        }
      }
    }

    // Press 'P' to print
    if ((e.key === "p" || e.key === "P") && e.ctrlKey) {
      e.preventDefault()
      window.print()
    }
  })

  // Add accessibility improvements
  function improveAccessibility() {
    // Add skip link
    const skipLink = document.createElement("a")
    skipLink.href = "#main-content"
    skipLink.textContent = "Skip to main content"
    skipLink.style.cssText = `
            position: absolute;
            top: -40px;
            left: 6px;
            background: #1e3c72;
            color: white;
            padding: 8px;
            text-decoration: none;
            border-radius: 4px;
            z-index: 10000;
            transition: top 0.3s ease;
        `

    skipLink.addEventListener("focus", function () {
      this.style.top = "6px"
    })

    skipLink.addEventListener("blur", function () {
      this.style.top = "-40px"
    })

    document.body.insertBefore(skipLink, document.body.firstChild)

    // Add main content landmark
    const mainContent = document.querySelector(".terms-main")
    if (mainContent) {
      mainContent.id = "main-content"
      mainContent.setAttribute("role", "main")
    }

    // Add navigation landmark to TOC
    const tocContainer = document.querySelector(".toc-container")
    if (tocContainer) {
      tocContainer.setAttribute("role", "navigation")
      tocContainer.setAttribute("aria-label", "Table of Contents")
    }
  }

  improveAccessibility()
})

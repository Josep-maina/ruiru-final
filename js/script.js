/**
 * Ruiru Technical and Vocational College
 * Main JavaScript File
 */

document.addEventListener("DOMContentLoaded", () => {
  // Initialize Bootstrap tooltips
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  var tooltipList = tooltipTriggerList.map((tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl))

  // Set active navigation based on current page
  setActiveNavigation()

  // Add smooth scrolling to all links
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      if (this.getAttribute("href") !== "#") {
        e.preventDefault()

        const targetId = this.getAttribute("href")
        const targetElement = document.querySelector(targetId)

        if (targetElement) {
          const navbarHeight = document.querySelector(".navbar").offsetHeight
          const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - navbarHeight

          window.scrollTo({
            top: targetPosition,
            behavior: "smooth",
          })
        }
      }
    })
  })

  // Add active class to nav items based on scroll position (for single page sections)
  window.addEventListener("scroll", () => {
    const scrollPosition = window.scrollY

    // Get all sections
    document.querySelectorAll("section[id]").forEach((section) => {
      const sectionTop = section.offsetTop - 100
      const sectionHeight = section.offsetHeight
      const sectionId = section.getAttribute("id")

      if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
        document.querySelectorAll(".navbar-nav .nav-link").forEach((link) => {
          if (link.getAttribute("href") === "#" + sectionId) {
            link.classList.add("active")
          } else if (link.getAttribute("href").startsWith("#")) {
            link.classList.remove("active")
          }
        })
      }
    })

    // Add background to navbar when scrolled
    const navbar = document.querySelector(".navbar")
    if (scrollPosition > 50) {
      navbar.classList.add("navbar-scrolled")
    } else {
      navbar.classList.remove("navbar-scrolled")
    }
  })

  // Initialize the carousel with custom settings
  var heroCarousel = document.querySelector("#hero")
  if (heroCarousel) {
    var carousel = new bootstrap.Carousel(heroCarousel, {
      interval: 4000,
      wrap: true,
    })
  }

  // Back to top button
  const backToTopButton = document.querySelector(".back-to-top")

  window.addEventListener("scroll", () => {
    if (window.pageYOffset > 300) {
      backToTopButton.classList.add("show")
    } else {
      backToTopButton.classList.remove("show")
    }
  })

  backToTopButton.addEventListener("click", () => {
    window.scrollTo({
      top: 0,
      behavior: "smooth",
    })
  })

  // Form validation for contact forms
  const forms = document.querySelectorAll(".needs-validation")

  Array.from(forms).forEach((form) => {
    form.addEventListener(
      "submit",
      (event) => {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }

        form.classList.add("was-validated")
      },
      false,
    )
  })
})

// Function to set active navigation based on current page
function setActiveNavigation() {
  const currentPage = window.location.pathname.split("/").pop() || "index.php"

  // Remove active class from all nav links
  document.querySelectorAll(".navbar-nav .nav-link").forEach((link) => {
    link.classList.remove("active")
  })

  // Add active class to current page link
  document.querySelectorAll(".navbar-nav .nav-link").forEach((link) => {
    const href = link.getAttribute("href")
    if (href === currentPage || (currentPage === "" && href === "index.php")) {
      link.classList.add("active")
    }

    // Special case for department pages - highlight the Departments dropdown
    if (
      (currentPage === "electrical.php" ||
        currentPage === "building.php" ||
        currentPage === "computing.php" ||
        currentPage === "hospitality.php" ||
        currentPage === "fashion.php" ||
        currentPage === "business.php") &&
      href === "#" &&
      link.getAttribute("id") === "departmentsDropdown"
    ) {
      link.classList.add("active")
    }
  })
}

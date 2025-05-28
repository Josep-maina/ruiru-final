/**
 * Gallery JavaScript Functions
 */

document.addEventListener("DOMContentLoaded", () => {
  // Initialize gallery
  initializeGallery()

  // Initialize filters
  initializeFilters()

  // Initialize load more functionality
  initializeLoadMore()

  // Initialize admin features (if admin is logged in)
  if (isAdmin()) {
    initializeAdminFeatures()
  }
})

// Gallery initialization
function initializeGallery() {
  // Add loading animation to images
  const images = document.querySelectorAll(".gallery-image")
  images.forEach((img) => {
    img.addEventListener("load", function () {
      this.style.opacity = "1"
    })
  })

  // Initialize masonry layout (optional)
  // initializeMasonry();
}

// Filter functionality
function initializeFilters() {
  const filterBtns = document.querySelectorAll(".filter-btn")
  const galleryItems = document.querySelectorAll(".gallery-item")

  filterBtns.forEach((btn) => {
    btn.addEventListener("click", function () {
      // Remove active class from all buttons
      filterBtns.forEach((b) => b.classList.remove("active"))

      // Add active class to clicked button
      this.classList.add("active")

      const filter = this.getAttribute("data-filter")

      // Filter gallery items
      galleryItems.forEach((item) => {
        const category = item.getAttribute("data-category")

        if (filter === "all" || category === filter) {
          item.style.display = "block"
          item.classList.remove("hide")
          item.classList.add("show")
        } else {
          item.classList.remove("show")
          item.classList.add("hide")
          setTimeout(() => {
            item.style.display = "none"
          }, 300)
        }
      })

      // Update gallery statistics
      updateGalleryStats(filter)
    })
  })
}

// Load more functionality
function initializeLoadMore() {
  const loadMoreBtn = document.getElementById("loadMoreBtn")
  let currentPage = 1
  const itemsPerPage = 12

  loadMoreBtn.addEventListener("click", function () {
    // Show loading state
    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...'
    this.disabled = true

    // Simulate API call to load more images
    setTimeout(() => {
      loadMoreImages(currentPage + 1)
      currentPage++

      // Reset button
      this.innerHTML = '<i class="fas fa-plus"></i> Load More Photos'
      this.disabled = false
    }, 1000)
  })
}

// Lightbox functionality
function openLightbox(imageSrc, title, description) {
  const modal = new bootstrap.Modal(document.getElementById("lightboxModal"))
  const lightboxImage = document.getElementById("lightboxImage")
  const lightboxTitle = document.getElementById("lightboxModalLabel")
  const lightboxDescription = document.getElementById("lightboxDescription")

  lightboxImage.src = imageSrc
  lightboxImage.alt = title
  lightboxTitle.textContent = title
  lightboxDescription.textContent = description

  // Store current image data for sharing/downloading
  window.currentLightboxImage = {
    src: imageSrc,
    title: title,
    description: description,
  }

  modal.show()
}

// Download image functionality
function downloadImage() {
  if (window.currentLightboxImage) {
    const link = document.createElement("a")
    link.href = window.currentLightboxImage.src
    link.download = window.currentLightboxImage.title.replace(/\s+/g, "_") + ".jpg"
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
  }
}

// Share image functionality
function shareImage() {
  if (window.currentLightboxImage && navigator.share) {
    navigator.share({
      title: window.currentLightboxImage.title,
      text: window.currentLightboxImage.description,
      url: window.location.href,
    })
  } else {
    // Fallback: copy to clipboard
    const url = window.location.href
    navigator.clipboard.writeText(url).then(() => {
      alert("Link copied to clipboard!")
    })
  }
}

// Update gallery statistics
function updateGalleryStats(filter) {
  const visibleItems = document.querySelectorAll('.gallery-item.show, .gallery-item[style="display: block"]')
  const statsElement = document.querySelector(".stat-item h3")

  if (filter === "all") {
    statsElement.textContent = "200+"
  } else {
    statsElement.textContent = visibleItems.length + "+"
  }
}

// Load more images (for admin integration)
function loadMoreImages(page) {
  // This would typically make an AJAX call to your backend
  // For now, we'll simulate it
  console.log("Loading page:", page)

  // Example: fetch('/api/gallery/images?page=' + page)
  //     .then(response => response.json())
  //     .then(data => {
  //         appendImagesToGallery(data.images);
  //     });
}

// Admin features
function initializeAdminFeatures() {
  // Add admin controls
  const adminControls = createAdminControls()
  document.body.appendChild(adminControls)

  // Initialize drag and drop upload
  initializeDragAndDrop()

  // Add edit/delete buttons to gallery items
  addAdminButtons()
}

function createAdminControls() {
  const controls = document.createElement("div")
  controls.className = "admin-controls"
  controls.innerHTML = `
        <h6>Admin Controls</h6>
        <button class="btn btn-primary btn-sm mb-2" onclick="toggleAdminMode()">
            <i class="fas fa-edit"></i> Edit Mode
        </button>
        <button class="btn btn-success btn-sm mb-2" onclick="showUploadModal()">
            <i class="fas fa-upload"></i> Upload Images
        </button>
        <button class="btn btn-warning btn-sm" onclick="manageCategories()">
            <i class="fas fa-tags"></i> Categories
        </button>
    `
  return controls
}

function initializeDragAndDrop() {
  // Drag and drop functionality for admin
  const galleryGrid = document.getElementById("gallery-grid")

  galleryGrid.addEventListener("dragover", function (e) {
    e.preventDefault()
    this.classList.add("dragover")
  })

  galleryGrid.addEventListener("dragleave", function (e) {
    this.classList.remove("dragover")
  })

  galleryGrid.addEventListener("drop", function (e) {
    e.preventDefault()
    this.classList.remove("dragover")

    const files = e.dataTransfer.files
    handleFileUpload(files)
  })
}

function addAdminButtons() {
  const galleryItems = document.querySelectorAll(".gallery-item")

  galleryItems.forEach((item) => {
    const adminBtns = document.createElement("div")
    adminBtns.className = "admin-buttons position-absolute top-0 end-0 p-2"
    adminBtns.style.display = "none"
    adminBtns.innerHTML = `
            <button class="btn btn-sm btn-warning me-1" onclick="editImage(this)">
                <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-sm btn-danger" onclick="deleteImage(this)">
                <i class="fas fa-trash"></i>
            </button>
        `

    item.querySelector(".gallery-card").appendChild(adminBtns)
  })
}

// Helper functions
function isAdmin() {
  // Check if user is admin (implement your auth logic)
  return localStorage.getItem("isAdmin") === "true"
}

function toggleAdminMode() {
  const adminButtons = document.querySelectorAll(".admin-buttons")
  adminButtons.forEach((btn) => {
    btn.style.display = btn.style.display === "none" ? "block" : "none"
  })
}

function showUploadModal() {
  // Show upload modal (implement your upload UI)
  alert("Upload modal would open here")
}

function manageCategories() {
  // Show category management modal
  alert("Category management would open here")
}

function editImage(button) {
  // Edit image functionality
  alert("Edit image functionality")
}

function deleteImage(button) {
  if (confirm("Are you sure you want to delete this image?")) {
    const galleryItem = button.closest(".gallery-item")
    galleryItem.remove()
  }
}

function handleFileUpload(files) {
  // Handle file upload
  console.log("Files to upload:", files)
  // Implement your upload logic here
}

// Keyboard navigation for lightbox
document.addEventListener("keydown", (e) => {
  const modal = document.getElementById("lightboxModal")
  if (modal.classList.contains("show")) {
    if (e.key === "ArrowLeft") {
      // Previous image
      navigateLightbox("prev")
    } else if (e.key === "ArrowRight") {
      // Next image
      navigateLightbox("next")
    }
  }
})

function navigateLightbox(direction) {
  // Implement navigation between images in lightbox
  console.log("Navigate:", direction)
}

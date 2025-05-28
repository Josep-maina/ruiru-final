/**
 * Contact Form JavaScript
 */

document.addEventListener("DOMContentLoaded", () => {
  // Initialize contact form
  initializeContactForm()

  // Initialize form validation
  initializeFormValidation()

  // Initialize map features
  initializeMapFeatures()
})

// Contact form initialization
function initializeContactForm() {
  const contactForm = document.getElementById("contactForm")
  const submitBtn = document.getElementById("submitBtn")

  if (contactForm) {
    contactForm.addEventListener("submit", handleFormSubmission)
  }

  // Auto-resize textarea
  const messageTextarea = document.getElementById("message")
  if (messageTextarea) {
    messageTextarea.addEventListener("input", autoResizeTextarea)
  }

  // Phone number formatting
  const phoneInput = document.getElementById("phone")
  if (phoneInput) {
    phoneInput.addEventListener("input", formatPhoneNumber)
  }
}

// Handle form submission
async function handleFormSubmission(e) {
  e.preventDefault()

  const form = e.target
  const submitBtn = document.getElementById("submitBtn")
  const successMessage = document.getElementById("successMessage")
  const errorMessage = document.getElementById("errorMessage")

  // Validate form
  if (!form.checkValidity()) {
    form.classList.add("was-validated")
    return
  }

  // Check honeypot field
  const honeypot = form.querySelector('input[name="website"]')
  if (honeypot && honeypot.value) {
    // Likely spam, silently ignore
    simulateSuccess()
    return
  }

  // Show loading state
  submitBtn.classList.add("btn-loading")
  submitBtn.disabled = true
  submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'

  try {
    // Collect form data
    const formData = new FormData(form)

    // Send form data to backend
    const response = await fetch("process_contact.php", {
      method: "POST",
      body: formData,
    })

    // Check if response is ok
    if (!response.ok) {
      throw new Error(`Server responded with status: ${response.status}`)
    }

    // Parse response
    const result = await response.json()

    if (result.success) {
      // Show success message
      successMessage.classList.remove("d-none")
      errorMessage.classList.add("d-none")

      // Add animation
      form.classList.add("form-submitted")

      // Scroll to success message
      successMessage.scrollIntoView({ behavior: "smooth", block: "center" })

      // Track form submission (analytics)
      trackFormSubmission("contact", formData.get("subject") || "general")

      // Reset form after successful submission
      form.reset()
      form.classList.remove("was-validated")
    } else {
      throw new Error(result.message || "Form submission failed")
    }
  } catch (error) {
    console.error("Form submission error:", error)

    // Show error message
    errorMessage.textContent = error.message || "There was a problem sending your message. Please try again."
    errorMessage.classList.remove("d-none")
    successMessage.classList.add("d-none")
  } finally {
    // Reset button state
    submitBtn.classList.remove("btn-loading")
    submitBtn.disabled = false
    submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Send Message'
  }
}

// Form validation
function initializeFormValidation() {
  const forms = document.querySelectorAll(".needs-validation")

  forms.forEach((form) => {
    const inputs = form.querySelectorAll("input, textarea, select")

    inputs.forEach((input) => {
      input.addEventListener("blur", validateField)
      input.addEventListener("input", clearValidation)
    })
  })
}

function validateField(e) {
  const field = e.target
  const form = field.closest("form")

  if (field.checkValidity()) {
    field.classList.remove("is-invalid")
    field.classList.add("is-valid")
  } else {
    field.classList.remove("is-valid")
    field.classList.add("is-invalid")
  }
}

function clearValidation(e) {
  const field = e.target
  field.classList.remove("is-invalid", "is-valid")
}

// Auto-resize textarea
function autoResizeTextarea(e) {
  const textarea = e.target
  textarea.style.height = "auto"
  textarea.style.height = textarea.scrollHeight + "px"
}

// Format phone number
function formatPhoneNumber(e) {
  const input = e.target
  let value = input.value.replace(/\D/g, "")

  // Add Kenya country code if not present
  if (value.length > 0 && !value.startsWith("254")) {
    if (value.startsWith("0")) {
      value = "254" + value.substring(1)
    } else if (value.length === 9) {
      value = "254" + value
    }
  }

  // Format the number
  if (value.length >= 3) {
    value = "+" + value.substring(0, 3) + " " + value.substring(3)
  }

  input.value = value
}

// Map features
function initializeMapFeatures() {
  const mapWrapper = document.querySelector(".map-wrapper")
  const iframe = mapWrapper?.querySelector("iframe")

  if (iframe) {
    // Add click handler to show larger map
    iframe.addEventListener("click", () => {
      window.open(iframe.src, "_blank")
    })

    // Add loading state
    iframe.addEventListener("load", () => {
      mapWrapper.classList.add("loaded")
    })
  }
}

// Utility functions
function trackFormSubmission(formType, category) {
  // Google Analytics or other tracking
  if (typeof gtag !== "undefined") {
    gtag("event", "form_submission", {
      event_category: "Contact",
      event_label: formType,
      value: category,
    })
  }
}

// Simulate success for spam submissions
function simulateSuccess() {
  const form = document.getElementById("contactForm")
  const successMessage = document.getElementById("successMessage")
  const errorMessage = document.getElementById("errorMessage")
  
  // Show success message
  successMessage.classList.remove("d-none")
  errorMessage.classList.add("d-none")
  
  // Reset form
  form.reset()
  
  // This tricks bots into thinking their submission worked
  console.log("Honeypot triggered - spam submission detected")
}

// Add a function to test the contact form submission
function testContactSubmission() {
  const form = document.getElementById("contactForm")
  if (!form) return
  
  // Fill in test data
  form.querySelector("#name").value = "Test User"
  form.querySelector("#email").value = "test@example.com"
  form.querySelector("#phone").value = "+254 712345678"
  form.querySelector("#subject").value = "Test Message"
  form.querySelector("#message").value = "This is a test message from the contact form."
  form.querySelector("#privacy").checked = true
  
  // Submit the form
  const submitEvent = new Event("submit", { cancelable: true })
  form.dispatchEvent(submitEvent)
}

// Uncomment to enable test function
// window.testContactForm = testContactSubmission

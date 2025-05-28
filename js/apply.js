/**
 * Application Form JavaScript
 */

let currentStep = 1
const totalSteps = 4

document.addEventListener("DOMContentLoaded", () => {
  // Initialize application form
  initializeApplicationForm()

  // Initialize form validation
  initializeFormValidation()

  // Initialize step navigation
  initializeStepNavigation()

  // Initialize form auto-save
  initializeAutoSave()
})

// Application form initialization
function initializeApplicationForm() {
  const applicationForm = document.getElementById("applicationForm")

  if (applicationForm) {
    applicationForm.addEventListener("submit", handleApplicationSubmission)
  }

  // Phone number formatting
  const phoneInputs = document.querySelectorAll('input[type="tel"]')
  phoneInputs.forEach((input) => {
    input.addEventListener("input", formatPhoneNumber)
  })

  // Date validation
  const dateInput = document.getElementById("dateOfBirth")
  if (dateInput) {
    // Set max date to 18 years ago
    const maxDate = new Date()
    maxDate.setFullYear(maxDate.getFullYear() - 15)
    dateInput.max = maxDate.toISOString().split("T")[0]

    // Set min date to 50 years ago
    const minDate = new Date()
    minDate.setFullYear(minDate.getFullYear() - 50)
    dateInput.min = minDate.toISOString().split("T")[0]
  }

  // Course selection change handler
  const courseSelect = document.getElementById("courseOfInterest")
  if (courseSelect) {
    courseSelect.addEventListener("change", handleCourseSelection)
  }
}

// Handle form submission
async function handleApplicationSubmission(e) {
  e.preventDefault()

  const form = e.target
  const submitBtn = document.getElementById("submitApplication")
  const successMessage = document.getElementById("successMessage")
  const errorMessage = document.getElementById("errorMessage")

  // Validate all steps
  if (!validateAllSteps()) {
    showError("Please complete all required fields in all steps.")
    return
  }

  // Show loading state
  submitBtn.classList.add("btn-loading")
  submitBtn.disabled = true
  submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...'

  try {
    // Collect form data
    const formData = new FormData(form)

    // Add application metadata
    formData.append("applicationDate", new Date().toISOString())

    // Send application data to backend for database storage
    const response = await fetch("process_application.php", {
      method: "POST",
      body: formData,
    })

    // Parse response
    const result = await response.json()

    if (result.success) {
      // Show success message
      successMessage.classList.remove("d-none")
      errorMessage.classList.add("d-none")

      // Update success message with application ID
      successMessage.innerHTML = `
                <i class="fas fa-check-circle me-2"></i>
                <strong>Application Submitted Successfully!</strong> 
                <p class="mb-0 mt-2">
                    Your application ID is: <strong>${result.applicationId}</strong><br>
                    You will receive a confirmation email shortly. Our admissions team will review your application and contact you within 3-5 business days.
                </p>
            `

      // Add animation
      form.classList.add("application-submitted")

      // Clear saved data
      clearSavedData()

      // Scroll to success message
      successMessage.scrollIntoView({ behavior: "smooth", block: "center" })

      // Track application submission
      trackApplicationSubmission(formData)

      // Reset form and navigation after successful submission
      setTimeout(() => {
        resetFormCompletely()
      }, 3000) // Wait 3 seconds before resetting
    } else {
      throw new Error(result.message || "Application submission failed")
    }
  } catch (error) {
    console.error("Application submission error:", error)
    showError(
      error.message ||
        "There was a problem submitting your application. Please check your internet connection and try again.",
    )
  } finally {
    // Reset button state
    submitBtn.classList.remove("btn-loading")
    submitBtn.disabled = false
    submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Submit Application'
  }
}

// Complete form reset function
function resetFormCompletely() {
  const form = document.getElementById("applicationForm")
  const successMessage = document.getElementById("successMessage")
  const errorMessage = document.getElementById("errorMessage")

  // Reset form data
  form.reset()

  // Remove all validation classes
  const allInputs = form.querySelectorAll("input, select, textarea")
  allInputs.forEach((input) => {
    input.classList.remove("is-valid", "is-invalid")
  })

  // Remove form validation class
  form.classList.remove("was-validated", "application-submitted")

  // Hide messages
  successMessage.classList.add("d-none")
  errorMessage.classList.add("d-none")

  // Reset step navigation
  currentStep = 1
  
  // Hide all steps
  for (let i = 1; i <= totalSteps; i++) {
    hideStep(i)
  }
  
  // Show only first step
  showStep(1)
  
  // Update progress and indicators
  updateProgress()
  updateStepIndicator()

  // Clear review content
  clearReviewContent()

  // Scroll to top of form
  const formWrapper = document.querySelector(".application-form-wrapper")
  if (formWrapper) {
    formWrapper.scrollIntoView({ behavior: "smooth", block: "start" })
  }
}

// Clear review content
function clearReviewContent() {
  const reviewSections = ["reviewPersonal", "reviewGuardian", "reviewPreferences"]
  reviewSections.forEach((sectionId) => {
    const section = document.getElementById(sectionId)
    if (section) {
      section.innerHTML = ""
    }
  })
}

// Step navigation functions
function nextStep() {
  if (validateCurrentStep()) {
    if (currentStep < totalSteps) {
      hideStep(currentStep)
      currentStep++
      showStep(currentStep)
      updateProgress()
      updateStepIndicator()

      // Generate review content for final step
      if (currentStep === 4) {
        generateReviewContent()
      }
    }
  }
}

function prevStep() {
  if (currentStep > 1) {
    hideStep(currentStep)
    currentStep--
    showStep(currentStep)
    updateProgress()
    updateStepIndicator()
  }
}

function showStep(step) {
  const stepElement = document.getElementById(`step${step}`)
  if (stepElement) {
    stepElement.classList.add("active")
    // Only scroll if not during reset
    if (step === currentStep) {
      stepElement.scrollIntoView({ behavior: "smooth", block: "start" })
    }
  }
}

function hideStep(step) {
  const stepElement = document.getElementById(`step${step}`)
  if (stepElement) {
    stepElement.classList.remove("active")
  }
}

function updateProgress() {
  const progressBar = document.querySelector(".progress-bar")
  if (progressBar) {
    const percentage = (currentStep / totalSteps) * 100
    progressBar.style.width = `${percentage}%`
  }
}

function updateStepIndicator() {
  const steps = document.querySelectorAll(".step")
  steps.forEach((step, index) => {
    const stepNumber = index + 1
    step.classList.remove("active", "completed")

    if (stepNumber === currentStep) {
      step.classList.add("active")
    } else if (stepNumber < currentStep) {
      step.classList.add("completed")
    }
  })
}

// Form validation
function validateCurrentStep() {
  const currentStepElement = document.getElementById(`step${currentStep}`)
  if (!currentStepElement) return false

  const requiredFields = currentStepElement.querySelectorAll("[required]")
  let isValid = true

  requiredFields.forEach((field) => {
    if (!field.checkValidity()) {
      field.classList.add("is-invalid")
      isValid = false
    } else {
      field.classList.remove("is-invalid")
      field.classList.add("is-valid")
    }
  })

  if (!isValid) {
    showError("Please fill in all required fields before proceeding.")
  }

  return isValid
}

function validateAllSteps() {
  let isValid = true

  for (let step = 1; step <= totalSteps - 1; step++) {
    const stepElement = document.getElementById(`step${step}`)
    if (!stepElement) continue

    const requiredFields = stepElement.querySelectorAll("[required]")

    requiredFields.forEach((field) => {
      if (!field.checkValidity()) {
        isValid = false
      }
    })
  }

  return isValid
}

function initializeFormValidation() {
  const form = document.getElementById("applicationForm")
  if (!form) return

  const inputs = form.querySelectorAll("input, select, textarea")

  inputs.forEach((input) => {
    input.addEventListener("blur", validateField)
    input.addEventListener("input", clearFieldValidation)
  })
}

function validateField(e) {
  const field = e.target

  if (field.checkValidity()) {
    field.classList.remove("is-invalid")
    field.classList.add("is-valid")
  } else {
    field.classList.remove("is-valid")
    field.classList.add("is-invalid")
  }
}

function clearFieldValidation(e) {
  const field = e.target
  field.classList.remove("is-invalid", "is-valid")
}

// Generate review content
function generateReviewContent() {
  const form = document.getElementById("applicationForm")
  if (!form) return

  const formData = new FormData(form)
  const data = Object.fromEntries(formData.entries())

  // Personal Details
  const personalReview = document.getElementById("reviewPersonal")
  if (personalReview) {
    personalReview.innerHTML = `
        <div class="review-item">
            <span class="review-label">Full Name:</span>
            <span class="review-value">${data.fullName || "Not provided"}</span>
        </div>
        <div class="review-item">
            <span class="review-label">Email:</span>
            <span class="review-value">${data.email || "Not provided"}</span>
        </div>
        <div class="review-item">
            <span class="review-label">Phone:</span>
            <span class="review-value">${data.phoneNumber || "Not provided"}</span>
        </div>
        <div class="review-item">
            <span class="review-label">Date of Birth:</span>
            <span class="review-value">${data.dateOfBirth || "Not provided"}</span>
        </div>
        <div class="review-item">
            <span class="review-label">Gender:</span>
            <span class="review-value">${data.gender || "Not provided"}</span>
        </div>
        <div class="review-item">
            <span class="review-label">KCSE Grade:</span>
            <span class="review-value">${data.kcseMeanGrade || "Not provided"}</span>
        </div>
        <div class="review-item">
            <span class="review-label">Course:</span>
            <span class="review-value">${getCourseDisplayName(data.courseOfInterest) || "Not provided"}</span>
        </div>
    `
  }

  // Guardian Information
  const guardianReview = document.getElementById("reviewGuardian")
  if (guardianReview) {
    guardianReview.innerHTML = `
        <div class="review-item">
            <span class="review-label">Guardian Name:</span>
            <span class="review-value">${data.guardianName || "Not provided"}</span>
        </div>
        <div class="review-item">
            <span class="review-label">Guardian Phone:</span>
            <span class="review-value">${data.guardianPhone || "Not provided"}</span>
        </div>
        <div class="review-item">
            <span class="review-label">Relationship:</span>
            <span class="review-value">${data.guardianRelationship || "Not specified"}</span>
        </div>
    `
  }

  // Preferences
  const preferencesReview = document.getElementById("reviewPreferences")
  if (preferencesReview) {
    preferencesReview.innerHTML = `
        <div class="review-item">
            <span class="review-label">Communication Method:</span>
            <span class="review-value">${data.communicationMethod || "Not selected"}</span>
        </div>
        <div class="review-item">
            <span class="review-label">Terms Accepted:</span>
            <span class="review-value">${data.termsConditions ? "Yes" : "No"}</span>
        </div>
        <div class="review-item">
            <span class="review-label">Marketing Consent:</span>
            <span class="review-value">${data.marketingConsent ? "Yes" : "No"}</span>
        </div>
    `
  }
}

// Utility functions
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

function getCourseDisplayName(courseValue) {
  const courseSelect = document.getElementById("courseOfInterest")
  if (!courseSelect) return courseValue

  const option = courseSelect.querySelector(`option[value="${courseValue}"]`)
  return option ? option.textContent : courseValue
}

function generateApplicationId() {
  const timestamp = Date.now().toString(36)
  const random = Math.random().toString(36).substr(2, 5)
  return `RTVC-${timestamp}-${random}`.toUpperCase()
}

function handleCourseSelection(e) {
  const selectedCourse = e.target.value
  // You can add course-specific logic here
  console.log("Selected course:", selectedCourse)
}

function showError(message) {
  const errorMessage = document.getElementById("errorMessage")
  if (errorMessage) {
    errorMessage.innerHTML = `
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Error!</strong> ${message}
    `
    errorMessage.classList.remove("d-none")
    errorMessage.scrollIntoView({ behavior: "smooth", block: "center" })
  }
}

// Auto-save functionality
function initializeAutoSave() {
  const form = document.getElementById("applicationForm")
  if (!form) return

  const inputs = form.querySelectorAll("input, select, textarea")

  inputs.forEach((input) => {
    input.addEventListener("change", saveFormData)
    input.addEventListener("input", debounce(saveFormData, 1000))
  })

  // Load saved data on page load
  loadSavedData()
}

function saveFormData() {
  const form = document.getElementById("applicationForm")
  if (!form) return

  const formData = new FormData(form)
  const data = Object.fromEntries(formData.entries())

  localStorage.setItem("rtvc_application_draft", JSON.stringify(data))
}

function loadSavedData() {
  const savedData = localStorage.getItem("rtvc_application_draft")

  if (savedData) {
    try {
      const data = JSON.parse(savedData)
      const form = document.getElementById("applicationForm")
      if (!form) return

      Object.keys(data).forEach((key) => {
        const field = form.querySelector(`[name="${key}"]`)
        if (field) {
          if (field.type === "radio" || field.type === "checkbox") {
            if (field.value === data[key] || data[key] === "on") {
              field.checked = true
            }
          } else {
            field.value = data[key]
          }
        }
      })
    } catch (error) {
      console.error("Error loading saved data:", error)
      localStorage.removeItem("rtvc_application_draft")
    }
  }
}

function clearSavedData() {
  localStorage.removeItem("rtvc_application_draft")
}

// Utility function for debouncing
function debounce(func, wait) {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout)
      func(...args)
    }
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}

// Analytics tracking
function trackApplicationSubmission(data) {
  if (typeof gtag !== "undefined") {
    gtag("event", "application_submission", {
      event_category: "Application",
      event_label: data.get ? data.get("courseOfInterest") : "unknown",
      value: data.get ? data.get("kcseMeanGrade") : "unknown",
    })
  }
}

// Initialize step navigation
function initializeStepNavigation() {
  // Set initial step
  currentStep = 1
  
  // Hide all steps first
  for (let i = 1; i <= totalSteps; i++) {
    hideStep(i)
  }
  
  // Show first step
  showStep(1)
  updateProgress()
  updateStepIndicator()
}

// Add manual reset button for testing (optional)
function addResetButton() {
  const resetBtn = document.createElement("button")
  resetBtn.type = "button"
  resetBtn.className = "btn btn-secondary btn-sm"
  resetBtn.innerHTML = "Reset Form"
  resetBtn.onclick = resetFormCompletely
  
  const formHeader = document.querySelector(".form-header")
  if (formHeader) {
    formHeader.appendChild(resetBtn)
  }
}

// Uncomment the line below to add a manual reset button for testing
 addResetButton()

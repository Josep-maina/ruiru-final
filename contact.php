<?php include 'includes/header.php'; ?>

<!-- Contact Hero Section -->
<section class="contact-hero">
    <div class="hero-overlay"></div>
    <img src="img/about-hero-bg.jpg" class="hero-bg-image" alt="Contact Background">
    <div class="container">
        <div class="row align-items-center min-vh-50">
            <div class="col-12 text-center hero-content">
                <h1 class="display-4 fw-bold text-white mb-3">Contact Us</h1>
                <p class="lead text-white">Get in touch with Ruiru Technical and Vocational College</p>
                <div class="hero-breadcrumb mt-4">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a href="index.php" class="text-warning">Home</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">Contact Us</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form and Information -->
<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <!-- Contact Form -->
            <div class="col-lg-8">
                <div class="contact-form-wrapper">
                    <h2 class="section-title mb-4">Send us a message</h2>
                    <p class="mb-4">Have a question or need more information? We'd love to hear from you. Send us a
                        message and we'll respond as soon as possible.</p>

                    <!-- Success Message (Hidden by default) -->
                    <div class="alert alert-success d-none" id="successMessage">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Thank You!</strong> Your message has been successfully sent. We'll respond within 24
                        hours.
                    </div>

                    <!-- Error Message (Hidden by default) -->
                    <div class="alert alert-danger d-none" id="errorMessage">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Error!</strong> There was a problem sending your message. Please try again.
                    </div>

                    <form id="contactForm" class="needs-validation" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                                <div class="invalid-feedback">
                                    Please provide your full name.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address <span
                                        class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                <div class="invalid-feedback">
                                    Please provide a valid email address.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="+254...">
                            </div>
                            <div class="col-md-6">
                                <label for="subject" class="form-label">Subject</label>
                                <select class="form-select" id="subject" name="subject">
                                    <option value="">Select a subject</option>
                                    <option value="admissions">Admissions Inquiry</option>
                                    <option value="courses">Course Information</option>
                                    <option value="fees">Fees and Payment</option>
                                    <option value="partnerships">Partnerships</option>
                                    <option value="general">General Inquiry</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="message" class="form-label">Message <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" id="message" name="message" rows="6"
                                    placeholder="Tell us how we can help you..." required></textarea>
                                <div class="invalid-feedback">
                                    Please provide your message.
                                </div>
                            </div>

                            <!-- Honeypot field for spam prevention -->
                            <input type="text" name="website" style="display: none;" tabindex="-1" autocomplete="off">

                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="privacy" name="privacy"
                                        required>
                                    <label class="form-check-label" for="privacy">
                                        I agree to the <a href="privacy-policy.php" target="_blank"
                                            class="text-success">Privacy Policy</a> <span class="text-danger">*</span>
                                    </label>
                                    <div class="invalid-feedback">
                                        You must agree to our privacy policy.
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                                    <i class="fas fa-paper-plane me-2"></i>Send Message
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="privacy-note mt-4">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>
                            By submitting this form, you agree to our <a href="privacy-policy.php" target="_blank"
                                class="text-success">privacy policy</a>.
                            We will only use your information to respond to your inquiry.
                        </small>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="col-lg-4">
                <div class="contact-info-wrapper">
                    <h3 class="section-title mb-4">Contact Information</h3>

                    <!-- Address -->
                    <div class="contact-info-item mb-4">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-details">
                            <h5>Address</h5>
                            <p>P.O. Box 416-00232, Ruiru<br>
                                Kiambu County, Kenya<br>
                                Located along Thika Superhighway, 2km from Ruiru town towards Nairobi</p>
                        </div>
                    </div>

                    <!-- Phone -->
                    <div class="contact-info-item mb-4">
                        <div class="contact-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="contact-details">
                            <h5>Phone Numbers</h5>
                            <p>
                                <a href="tel:+254746319919" class="contact-link">+254 746 319 919</a><br>
                                <a href="tel:+254789869499" class="contact-link">+254 789 869 499</a>
                            </p>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="contact-info-item mb-4">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-details">
                            <h5>Email</h5>
                            <p>
                                <a href="mailto:ruirutvc@gmail.com" target="_blank"
                                    class="contact-link">ruirutvc@gmail.com</a>
                            </p>
                        </div>
                    </div>

                    <!-- Office Hours -->
                    <div class="contact-info-item mb-4">
                        <div class="contact-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="contact-details">
                            <h5>Office Hours</h5>
                            <p>
                                <strong>Monday - Friday:</strong> 8:00 AM - 5:00 PM<br>
                                <strong>Weekends:</strong> Closed<br>
                                <strong>Public Holidays:</strong> Closed
                            </p>
                        </div>
                    </div>

                    <!-- Social Media -->
                    <div class="contact-info-item">
                        <div class="contact-icon">
                            <i class="fas fa-share-alt"></i>
                        </div>
                        <div class="contact-details">
                            <h5>Follow Us</h5>
                            <div class="social-links">
                                <a href="https://www.facebook.com/p/Ruiru-Technical-And-Vocational-College-100094577041531/"
                                    target="_blank" class="social-link" aria-label="Facebook">
                                    <i class="fab fa-facebook"></i>
                                </a>
                                <a href="https://twitter.com/ruirutvc" target="_blank" class="social-link"
                                    aria-label="Twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="https://www.instagram.com/ruirutvc/" target="_blank" class="social-link"
                                    aria-label="Instagram">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="https://www.tiktok.com/@ruiru_tvc" target="_blank" class="social-link"
                                    aria-label="Tiktok">
                                    <i class="fab fa-tiktok"></i>
                                </a>
                                <a href="https://wa.me/254746319919?text=Hi%21%20I'm%20interested%20in%20learning%20more%20about%20your%20programs%20at%20Ruiru%20Technical%20and%20Vocational%20College."
                                    class="social-link" target="_blank" aria-label="WhatsApp">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="section-title text-center mb-5">Our Location</h2>
        <div class="row">
            <div class="col-12">
                <div class="map-wrapper">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.996633679051!2d36.98737669999999!3d-1.1628445!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x182f4104bb395d15%3A0x70da85ef22b57f86!2sRuiru%20Technical%20and%20Vocational%20College!5e0!3m2!1sen!2sus!4v1737100754072!5m2!1sen!2sus"
                        width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Ruiru Technical and Vocational College Location">
                    </iframe>
                </div>
                <div class="map-info mt-3 text-center">
                    <p class="mb-2">
                        <strong>Directions:</strong> Located along Thika Superhighway, 2km from Ruiru town towards
                        Nairobi
                    </p>
                    <a href="https://maps.app.goo.gl/i3xB5WxeP9gwET2g7" target="_blank" class="btn btn-outline-success">
                        <i class="fas fa-directions me-2"></i> Get Directions
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5">
    <div class="container">
        <h2 class="section-title text-center mb-5">Frequently Asked Questions</h2>
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq1">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapse1">
                                What are the admission requirements?
                            </button>
                        </h2>
                        <div id="collapse1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Admission requirements vary by course level. Generally, you need a KCSE certificate with
                                specific grades depending on the course. Contact our admissions office for detailed
                                requirements for your preferred course.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapse2">
                                How can I apply for courses?
                            </button>
                        </h2>
                        <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                You can apply online through our website or visit our campus for in-person application.
                                Required documents include KCSE certificate, ID copy, and passport photos.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapse3">
                                What are the fee structures?
                            </button>
                        </h2>
                        <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Fee structures vary by course and level. We offer flexible payment plans and accept HELB
                                funding. Contact our finance office for detailed fee information and payment options.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapse4">
                                Do you offer accommodation?
                            </button>
                        </h2>
                        <div id="collapse4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                We can assist students in finding suitable accommodation near the college. We maintain a
                                list of approved hostels and boarding facilities in the area.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Actions -->
<section class="py-5 bg-success text-white">
    <div class="container">
        <div class="row text-center">
            <div class="col-12 mb-4">
                <h2>Need Immediate Assistance?</h2>
                <p class="lead">Choose the best way to reach us</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-3 col-sm-6">
                <div class="quick-action">
                    <a href="tel:+254746319919" class="btn btn-warning btn-lg w-100">
                        <i class="fas fa-phone mb-2 d-block"></i>
                        Call Now
                    </a>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="quick-action">
                    <a href="mailto:ruirutvc@gmail.com" target="_blank" class="btn btn-warning btn-lg w-100">
                        <i class="fas fa-envelope mb-2 d-block"></i>
                        Email Us
                    </a>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="quick-action">
                    <a href="https://wa.me/254746319919?text=Hi%21%20I'm%20interested%20in%20learning%20more%20about%20your%20programs%20at%20Ruiru%20Technical%20and%20Vocational%20College."
                        target="_blank" class="btn btn-warning btn-lg w-100">
                        <i class="fab fa-whatsapp mb-2 d-block"></i>
                        WhatsApp
                    </a>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="quick-action">
                    <a href="apply.php" class="btn btn-warning btn-lg w-100">
                        <i class="fas fa-graduation-cap mb-2 d-block"></i>
                        Apply Now
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
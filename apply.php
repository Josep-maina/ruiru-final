<?php include 'includes/header.php'; ?>

<!-- Apply Hero Section -->
<section class="apply-hero">
    <div class="hero-overlay"></div>
    <img src="img/about-hero-bg.jpg" class="hero-bg-image" alt="Apply Background">
    <div class="container">
        <div class="row align-items-center min-vh-50">
            <div class="col-12 text-center hero-content">
                <h1 class="display-4 fw-bold text-white mb-3">Online Admission Form</h1>
                <p class="lead text-white">Hello and welcome. This is Ruiru Technical and Vocational College online admission application form. Kindly fill all the required fields below.</p>
                <div class="hero-breadcrumb mt-4">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a href="index.php" class="text-warning">Home</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">Apply Now</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Application Form Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <!-- Progress Indicator -->
                <div class="application-progress mb-4">
                    <div class="progress-steps">
                        <div class="step active" data-step="1">
                            <div class="step-number">1</div>
                            <div class="step-label">Personal Details</div>
                        </div>
                        <div class="step" data-step="2">
                            <div class="step-number">2</div>
                            <div class="step-label">Guardian Info</div>
                        </div>
                        <div class="step" data-step="3">
                            <div class="step-number">3</div>
                            <div class="step-label">Preferences</div>
                        </div>
                        <div class="step" data-step="4">
                            <div class="step-number">4</div>
                            <div class="step-label">Review</div>
                        </div>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 25%"></div>
                    </div>
                </div>

                <!-- Application Form -->
                <div class="application-form-wrapper">
                    <div class="form-header text-center mb-4">
                        <h2 class="section-title">Admission Application Form</h2>
                        <p class="text-muted">Please provide accurate information. All fields marked with <span class="text-danger">*</span> are required.</p>
                    </div>

                    <!-- Success Message (Hidden by default) -->
                    <div class="alert alert-success d-none" id="successMessage">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Application Submitted Successfully!</strong> 
                        <p class="mb-0 mt-2">Thank you for your application. You will be contacted by the admissions office for the next steps.</p>
                    </div>

                    <!-- Error Message (Hidden by default) -->
                    <div class="alert alert-danger d-none" id="errorMessage">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Error!</strong> There was a problem submitting your application. Please check all fields and try again.
                    </div>

                    <form id="applicationForm" class="needs-validation" novalidate>
                        <!-- Step 1: Personal Details -->
                        <div class="form-step active" id="step1">
                            <h3 class="step-title">
                                <i class="fas fa-user me-2"></i>Personal Details
                            </h3>
                            
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="fullName" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="fullName" name="fullName" required>
                                    <div class="invalid-feedback">Please provide your full name.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                    <div class="invalid-feedback">Please provide a valid email address.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="idNumber" class="form-label">ID/Maisha Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="idNumber" name="idNumber" required>
                                    <div class="invalid-feedback">Please provide your ID/Maisha number.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="phoneNumber" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber" placeholder="+254..." required>
                                    <div class="invalid-feedback">Please provide your phone number.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="dateOfBirth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="dateOfBirth" name="dateOfBirth" required>
                                    <div class="invalid-feedback">Please provide your date of birth.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                    <select class="form-select" id="gender" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                    <div class="invalid-feedback">Please select your gender.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="nationality" class="form-label">Nationality <span class="text-danger">*</span></label>
                                    <select class="form-select" id="nationality" name="nationality" required>
                                        <option value="">Select Nationality</option>
                                        <option value="kenyan">Kenyan</option>
                                        <option value="ugandan">Ugandan</option>
                                        <option value="tanzanian">Tanzanian</option>
                                        <option value="rwandan">Rwandan</option>
                                        <option value="burundian">Burundian</option>
                                        <option value="south_sudanese">South Sudanese</option>
                                        <option value="ethiopian">Ethiopian</option>
                                        <option value="somali">Somali</option>
                                        <option value="other">Other</option>
                                    </select>
                                    <div class="invalid-feedback">Please select your nationality.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="kcseMeanGrade" class="form-label">KCSE Mean Grade <span class="text-danger">*</span></label>
                                    <select class="form-select" id="kcseMeanGrade" name="kcseMeanGrade" required>
                                        <option value="">Select Mean Grade</option>
                                        <option value="A">A</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B">B</option>
                                        <option value="B-">B-</option>
                                        <option value="C+">C+</option>
                                        <option value="C">C</option>
                                        <option value="C-">C-</option>
                                        <option value="D+">D+</option>
                                        <option value="D">D</option>
                                        <option value="D-">D-</option>
                                        <option value="E">E</option>
                                    </select>
                                    <div class="invalid-feedback">Please select your KCSE mean grade.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="courseOfInterest" class="form-label">Course of Interest <span class="text-danger">*</span></label>
                                    <select class="form-select" id="courseOfInterest" name="courseOfInterest" required>
                                        <option value="">Select Course</option>
                                        <optgroup label="Computing and Informatics">
                                            <option value="ict_level_6">Information Communication Technology - Level 6</option>
                                            <option value="ict_level_5">Information Communication Technology - Level 5</option>
                                            <option value="ict_level_4">Information Communication Technology - Level 4</option>
                                            <option value="cyber_security_level_6">Cyber Security - Level 6</option>
                                            <option value="cyber_security_level_5">Cyber Security - Level 5</option>
                                            <option value="computer_applications">Computer Applications - Level 3</option>
                                        </optgroup>
                                        <optgroup label="Business and Entrepreneurship">
                                            <option value="business_management">Business Management</option>
                                            <option value="social_work_level_6">Social Work - Level 6</option>
                                            <option value="social_work_level_5">Social Work - Level 5</option>
                                            <option value="secretarial_studies">Secretarial Studies - Level 6</option>
                                        </optgroup>
                                        <optgroup label="Electrical Engineering">
                                            <option value="electrical_engineering_level_6">Electrical Engineering - Level 6</option>
                                            <option value="electrical_installation_level_5">Electrical Installation - Level 5</option>
                                            <option value="electrical_installation_level_4">Electrical Installation - Level 4</option>
                                            <option value="solar_pv_level_6">Solar PV Installation - Level 6</option>
                                            <option value="solar_pv_level_5">Solar PV Installation - Level 5</option>
                                            <option value="solar_pv_level_4">Solar PV Installation - Level 4</option>
                                        </optgroup>
                                        <optgroup label="Building and Civil Engineering">
                                            <option value="building_technology_level_6">Building Technology - Level 6</option>
                                            <option value="building_craft_level_5">Craft in Building - Level 5</option>
                                            <option value="masonry_level_4">Masonry - Level 4</option>
                                            <option value="plumbing_level_5">Plumbing - Level 5</option>
                                            <option value="plumbing_level_4">Plumbing - Level 4</option>
                                        </optgroup>
                                        <optgroup label="Hospitality and Institutional Management">
                                            <option value="food_production_level_6">Food Production - Level 6</option>
                                            <option value="food_production_level_5">Food Production - Level 5</option>
                                            <option value="food_production_level_4">Food Production - Level 4</option>
                                            <option value="baking_technology_level_6">Baking Technology - Level 6</option>
                                            <option value="baking_technology_level_5">Baking Technology - Level 5</option>
                                            <option value="food_beverage_service_level_6">Food & Beverage Service - Level 6</option>
                                            <option value="food_beverage_service_level_5">Food & Beverage Service - Level 5</option>
                                        </optgroup>
                                        <optgroup label="Fashion Design and Cosmetology">
                                            <option value="beauty_therapy_level_6">Beauty Therapy - Level 6</option>
                                            <option value="beauty_therapy_level_5">Beauty Therapy - Level 5</option>
                                            <option value="beauty_therapy_level_4">Beauty Therapy - Level 4</option>
                                            <option value="hair_dressing_level_6">Hair Dressing - Level 6</option>
                                            <option value="hair_dressing_level_5">Hair Dressing - Level 5</option>
                                            <option value="hair_dressing_level_4">Hair Dressing - Level 4</option>
                                        </optgroup>
                                    </select>
                                    <div class="invalid-feedback">Please select your course of interest.</div>
                                </div>

                                <div class="col-12">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="3" placeholder="Your current address (optional)"></textarea>
                                </div>

                                <div class="col-12">
                                    <label for="emergencyContact" class="form-label">Emergency Contact</label>
                                    <input type="text" class="form-control" id="emergencyContact" name="emergencyContact" placeholder="Emergency contact person and phone number (optional)">
                                </div>
                            </div>

                            <div class="step-navigation mt-4">
                                <button type="button" class="btn btn-success" onclick="nextStep()">
                                    Next <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Parent/Guardian Information -->
                        <div class="form-step" id="step2">
                            <h3 class="step-title">
                                <i class="fas fa-users me-2"></i>Parent/Guardian Information
                            </h3>
                            
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="guardianName" class="form-label">Parent/Guardian Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="guardianName" name="guardianName" required>
                                    <div class="invalid-feedback">Please provide parent/guardian name.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="guardianPhone" class="form-label">Parent/Guardian Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="guardianPhone" name="guardianPhone" placeholder="+254..." required>
                                    <div class="invalid-feedback">Please provide parent/guardian phone number.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="guardianRelationship" class="form-label">Relationship</label>
                                    <select class="form-select" id="guardianRelationship" name="guardianRelationship">
                                        <option value="">Select Relationship</option>
                                        <option value="father">Father</option>
                                        <option value="mother">Mother</option>
                                        <option value="guardian">Guardian</option>
                                        <option value="sibling">Sibling</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label for="guardianEmail" class="form-label">Parent/Guardian Email</label>
                                    <input type="email" class="form-control" id="guardianEmail" name="guardianEmail" placeholder="Optional">
                                </div>
                            </div>

                            <div class="step-navigation mt-4">
                                <button type="button" class="btn btn-outline-secondary" onclick="prevStep()">
                                    <i class="fas fa-arrow-left me-2"></i>Previous
                                </button>
                                <button type="button" class="btn btn-success" onclick="nextStep()">
                                    Next <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 3: Communication Preferences -->
                        <div class="form-step" id="step3">
                            <h3 class="step-title">
                                <i class="fas fa-comments me-2"></i>Communication Preferences
                            </h3>
                            
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Preferred Communication Method <span class="text-danger">*</span></label>
                                    <div class="communication-options">
                                        <div class="form-check communication-option">
                                            <input class="form-check-input" type="radio" name="communicationMethod" id="commEmail" value="email" required>
                                            <label class="form-check-label" for="commEmail">
                                                <i class="fas fa-envelope"></i>
                                                <span>Email</span>
                                                <small>Receive updates via email</small>
                                            </label>
                                        </div>
                                        <div class="form-check communication-option">
                                            <input class="form-check-input" type="radio" name="communicationMethod" id="commWhatsApp" value="whatsapp" required>
                                            <label class="form-check-label" for="commWhatsApp">
                                                <i class="fab fa-whatsapp"></i>
                                                <span>WhatsApp</span>
                                                <small>Receive updates via WhatsApp</small>
                                            </label>
                                        </div>
                                        <div class="form-check communication-option">
                                            <input class="form-check-input" type="radio" name="communicationMethod" id="commSMS" value="sms" required>
                                            <label class="form-check-label" for="commSMS">
                                                <i class="fas fa-sms"></i>
                                                <span>SMS</span>
                                                <small>Receive updates via SMS</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">Please select a communication method.</div>
                                </div>

                                <div class="col-12">
                                    <label for="additionalInfo" class="form-label">Additional Information</label>
                                    <textarea class="form-control" id="additionalInfo" name="additionalInfo" rows="4" placeholder="Any additional information you'd like to share (optional)"></textarea>
                                </div>

                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="termsConditions" name="termsConditions" required>
                                        <label class="form-check-label" for="termsConditions">
                                            I agree to the <a href="terms-conditions.php" target="_blank" class="text-success">Terms and Conditions</a> and <a href="privacy-policy.php" target="_blank" class="text-success">Privacy Policy</a> <span class="text-danger">*</span>
                                        </label>
                                        <div class="invalid-feedback">You must agree to the terms and conditions.</div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="marketingConsent" name="marketingConsent">
                                        <label class="form-check-label" for="marketingConsent">
                                            I consent to receive marketing communications about courses and events
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="step-navigation mt-4">
                                <button type="button" class="btn btn-outline-secondary" onclick="prevStep()">
                                    <i class="fas fa-arrow-left me-2"></i>Previous
                                </button>
                                <button type="button" class="btn btn-success" onclick="nextStep()">
                                    Review Application <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 4: Review and Submit -->
                        <div class="form-step" id="step4">
                            <h3 class="step-title">
                                <i class="fas fa-check-circle me-2"></i>Review Your Application
                            </h3>
                            
                            <div class="application-review">
                                <div class="review-section">
                                    <h4>Personal Details</h4>
                                    <div class="review-content" id="reviewPersonal"></div>
                                </div>
                                
                                <div class="review-section">
                                    <h4>Parent/Guardian Information</h4>
                                    <div class="review-content" id="reviewGuardian"></div>
                                </div>
                                
                                <div class="review-section">
                                    <h4>Communication Preferences</h4>
                                    <div class="review-content" id="reviewPreferences"></div>
                                </div>
                            </div>

                            <div class="step-navigation mt-4">
                                <button type="button" class="btn btn-outline-secondary" onclick="prevStep()">
                                    <i class="fas fa-arrow-left me-2"></i>Previous
                                </button>
                                <button type="submit" class="btn btn-success btn-lg" id="submitApplication">
                                    <i class="fas fa-paper-plane me-2"></i>Submit Application
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Application Information -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <h2 class="section-title text-center mb-5">Application Process</h2>
                <div class="row g-4">
                    <div class="col-md-3">
                        <div class="process-step text-center">
                            <div class="process-icon">
                                <i class="fas fa-edit"></i>
                            </div>
                            <h4>1. Apply Online</h4>
                            <p>Fill out the application form with accurate information</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="process-step text-center">
                            <div class="process-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <h4>2. Submit Documents</h4>
                            <p>Provide required documents for verification</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="process-step text-center">
                            <div class="process-icon">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <h4>3. Review Process</h4>
                            <p>Our admissions team will review your application</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="process-step text-center">
                            <div class="process-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h4>4. Get Admitted</h4>
                            <p>Receive admission confirmation and join us</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

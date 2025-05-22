        $(document).ready(function() {

            const categories = @json($categories);

            const container = $('#categories-container'); // Target the container by ID

            let html = '';
            $.each(categories, function(index, category) {
                html += `
            <div class="col">
                <div class="card border h-100 category-card text-center rounded p-2" data-category="${category.id}">
                    <div class="card-body">
                         ${category.image ? `<img class="img-fluid w-25 mb-2" src="uploads/images/category/${category.image}">` : ""}
                        <h5 class="card-title">${category.title}</h5>
                        <p class="card-text">${category.body}</p>
                    </div>
                </div>
            </div>
        `;
            });

            container.html(html); // Insert all generated HTML at once


            const employees = @json($employees);
            // console.log(employees);

            // Booking state
            let bookingState = {
                currentStep: 1,
                selectedCategory: null,
                selectedService: null,
                selectedEmployee: null,
                selectedDate: null,
                selectedTime: null
            };

            // Initialize the booking system
            updateProgressBar();
            generateCalendar();

            // Step navigation
            $("#next-step").click(function() {
                const currentStep = bookingState.currentStep;

                // Validate current step before proceeding
                if (!validateStep(currentStep)) {
                    return;
                }

                if (currentStep < 5) {
                    goToStep(currentStep + 1);
                } else {
                    // Submit booking
                    if ($("#customer-info-form")[0].checkValidity()) {
                        submitBooking();
                    } else {
                        $("#customer-info-form")[0].reportValidity();
                    }
                }
            });

            $("#prev-step").click(function() {
                if (bookingState.currentStep > 1) {
                    goToStep(bookingState.currentStep - 1);
                }
            });

            // Category selection
            $(document).on("click", ".category-card", function() {
                $(".category-card").removeClass("selected");
                $(this).addClass("selected");

                const categoryId = $(this).data("category");
                // console.log(categoryId);
                bookingState.selectedCategory = categoryId;

                // Reset subsequent selections
                bookingState.selectedService = null;
                bookingState.selectedEmployee = null;
                bookingState.selectedDate = null;
                bookingState.selectedTime = null;

                // Update the service step with services for this category
                updateServicesStep(categoryId);
            });

            // Service selection
            $(document).on("click", ".service-card", function() {
                $(".service-card").removeClass("selected");
                $(this).addClass("selected");

                const serviceId = $(this).data("service");
                const serviceTitle = $(this).find('.card-title').text();
                const servicePrice = $(this).find('.fw-bold').text().replace('TZS ', '').replace(/,/g, '');
                const serviceDuration = $(this).find('.card-text:contains("Duration:")').text().replace('Duration: ', '');

                // Store the selected service in booking state
                bookingState.selectedService = {
                    id: serviceId,
                    title: serviceTitle,
                    price: servicePrice,
                    duration: serviceDuration
                };

                // Reset subsequent selections
                bookingState.selectedEmployee = null;
                bookingState.selectedDate = null;
                bookingState.selectedTime = null;

                // Clear previous selections UI
                $(".employee-card").removeClass("selected");
                $("#selected-date").text("");
                $("#selected-time").text("");
                $("#employees-container").empty(); // Clear previous employees while loading new ones

                // Show loading state for employees
                $("#employees-container").html(
                    '<div class="col-12 text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>'
                );

                // Update the employee step with employees for this service
                updateEmployeesStep(serviceId);

                // Show the employee step immediately (loading will happen inside updateEmployeesStep)
                $("#services-step").addClass("d-none");
                $("#employees-step").removeClass("d-none");
                $(".step-indicator[data-step='services']").removeClass("active current").addClass(
                    "completed");
                $(".step-indicator[data-step='employees']").addClass("active current");
            });

            // Employee selection
            $(document).on("click", ".employee-card", function() {
                $(".employee-card").removeClass("selected");
                $(this).addClass("selected");

                const employeeId = $(this).data("employee");
                // alert(employeeId);
                const employee = employees.find(e => e.id === employeeId);

                bookingState.selectedEmployee = employee;

                // Reset subsequent selections
                bookingState.selectedDate = null;
                bookingState.selectedTime = null;

                // Update the calendar
                updateCalendar();
            });


            // Date selection
            $(document).on("click", ".calendar-day:not(.disabled)", function() {
                $(".calendar-day").removeClass("selected");
                $(this).addClass("selected");

                const date = $(this).data("date");
                bookingState.selectedDate = date;

                // Reset time selection
                bookingState.selectedTime = null;

                // Update time slots based on employee availability
                updateTimeSlots(date);
            });

            // Time slot selection (remove all other handlers for this)
            $(document).off("click", ".time-slot:not(.disabled)");
            $(document).on("click", ".time-slot:not(.disabled)", function() {
                $(".time-slot").removeClass("selected active");
                $(this).addClass("selected active");

                const display = $(this).data("time");
                const start = $(this).data("start");
                const end = $(this).data("end");
                bookingState.selectedTime = {
                    display: display,
                    start: start,
                    end: end
                };
                updateSummary();
            });

            // Calendar navigation
            $("#prev-month").click(function() {
                navigateMonth(-1);
            });

            $("#next-month").click(function() {
                navigateMonth(1);
            });

            // Functions
            function goToStep(step) {
                // Hide all steps
                $(".booking-step").removeClass("active");

                // Show the target step
                $(`#step${step}`).addClass("active");

                // Update the step indicators
                $(".step").removeClass("active completed");

                for (let i = 1; i <= 5; i++) {
                    if (i < step) {
                        $(`.step[data-step="${i}"]`).addClass("completed");
                    } else if (i === step) {
                        $(`.step[data-step="${i}"]`).addClass("active");
                    }
                }

                // Update the current step
                bookingState.currentStep = step;

                // Update the navigation buttons
                updateNavigationButtons();

                // Update the progress bar
                updateProgressBar();

                // If we're on the confirmation step, update the summary
                if (step === 5) {
                    updateSummary();
                }

                // Scroll to top of booking container
                $(".booking-container")[0].scrollIntoView({
                    behavior: "smooth"
                });
            }


            function updateProgressBar() {
                const progress = ((bookingState.currentStep - 1) / 4) * 100;
                $(".progress-bar-steps .progress").css("width", `${progress}%`);
            }


            function updateNavigationButtons() {
                // Enable/disable previous button
                if (bookingState.currentStep === 1) {
                    $("#prev-step").prop("disabled", true);
                } else {
                    $("#prev-step").prop("disabled", false);
                }

                // Update next button text
                if (bookingState.currentStep === 5) {
                    $("#next-step").html('Confirm Booking <i class="bi bi-check-circle"></i>');
                } else {
                    $("#next-step").html('Next <i class="bi bi-arrow-right"></i>');
                }
            }


            function validateStep(step) {
                switch (step) {
                    case 1:
                        if (!bookingState.selectedCategory) {
                            alert("Please select a category");
                            return false;
                        }
                        return true;
                    case 2:
                        if (!bookingState.selectedService) {
                            alert("Please select a service");
                            return false;
                        }
                        return true;
                    case 3:
                        if (!bookingState.selectedEmployee) {
                            alert("Please select a staff member");
                            return false;
                        }
                        return true;
                    case 4:
                        if (!bookingState.selectedDate) {
                            alert("Please select a date");
                            return false;
                        }
                        if (!bookingState.selectedTime) {
                            alert("Please select a time slot");
                            return false;
                        }
                        return true;
                    default:
                        return true;
                }
            }


            function updateServicesStep(categoryId) {
                // Show loading state if needed
                $("#services-container").html(
                    '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>'
                );

                // Make AJAX request to get services for this category
                $.ajax({
                    url: `/categories/${categoryId}/services`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success && response.services) {
                            const services = response.services;

                            // Update category name display
                            $(".selected-category-name").text(
                                `Selected Category: ${services[0]?.category?.title || ''}`);

                            // Clear services container
                            $("#services-container").empty();

                            // Add services with animation delay
                            services.forEach((service, index) => {
                                // Determine the price display
                                let priceDisplay;
                                if (service.sale_price) {
                                    // If sale price exists, show both with strike-through on original price
                                    priceDisplay =
                                        `<span class="text-decoration-line-through text-muted">${service.price}</span> <span class=" fw-bold">${service.sale_price}</span>`;
                                } else {
                                    // If no sale price, just show regular price normally
                                    priceDisplay =
                                        `<span class="fw-bold">${service.price}</span>`;
                                }

                                const serviceCard = `
                                    <div class="col animate-slide-in" style="animation-delay: ${index * 100}ms">
                                        <div class="card border h-100 service-card text-center p-2" data-service="${service.id}">
                                            <div class="card-body">
                                                ${service.image ? `<img class="img-fluid rounded mb-2" src="uploads/images/service/${service.image}">` : ""}
                                                <h5 class="card-title mb-1">${service.title}</h5>
                                                <p class="card-text mb-1">${service.excerpt}</p>
                                                <p class="card-text">${priceDisplay}</p>
                                            </div>
                                        </div>
                                    </div>
                                `;

                                $("#services-container").append(serviceCard);
                            });
                        } else {
                            $("#services-container").html(
                                '<div class="col-12 text-center py-5"><p>No services available for this category.</p></div>'
                            );
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        $("#services-container").html(
                            '<div class="col-12 text-center py-5"><p>Error loading services. Please try again.</p></div>'
                        );
                    }
                });
            }



            function updateEmployeesStep(serviceId) {
                // Show loading state
                $("#employees-container").html(
                    '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>'
                );

                // Make AJAX request to get employees for this service
                $.ajax({
                    url: `/services/${serviceId}/employees`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success && response.employees) {
                            const employees = response.employees;
                            const service = response.service;

                             // Determine the price display
                             let priceDisplay;
                                if (service.sale_price) {
                                    // If sale price exists, show both with strike-through on original price
                                    priceDisplay =
                                        `<span class="">${service.sale_price}</span>`;
                                } else {
                                    // If no sale price, just show regular price normally
                                    priceDisplay =
                                        `<span class="fw-bold">${service.price}</span>`;
                                }

                            // Update service name display
                            $(".selected-service-name").html(
                                `Selected Service: ${service.title} (${bookingState.selectedService.price})`);

                            // Clear employees container
                            $("#employees-container").empty();

                            // Add employees with animation delay
                            employees.forEach((employee, index) => {
                                const employeeCard = `
                                <div class="col animate-slide-in" style="animation-delay: ${index * 100}ms">
                                    <div class="card border h-100 employee-card text-center p-2" data-employee="${employee.id}">
                                        <div class="card-body">
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                                                ${employee.user.image ?
                                                    `<img src="uploads/images/profile/${employee.user.image}" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">` :
                                                    `<i class="bi bi-person text-primary" style="font-size: 2rem;"></i>`
                                                }
                                            </div>
                                            <h5 class="card-title">${employee.user.name}</h5>
                                            <p class="card-text text-muted">${employee.position || 'Professional'}</p>
                                        </div>
                                    </div>
                                </div>
                            `;
                                $("#employees-container").append(employeeCard);
                            });
                        } else {
                            $("#employees-container").html(
                                '<div class="col-12 text-center py-5"><p>No employees available for this service.</p></div>'
                            );
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        $("#employees-container").html(
                            '<div class="col-12 text-center py-5"><p>Error loading employees. Please try again.</p></div>'
                        );
                    }
                });
            }

            function generateCalendar() {
                const today = new Date();
                const currentMonth = today.getMonth();
                const currentYear = today.getFullYear();

                renderCalendar(currentMonth, currentYear);
            }

            function renderCalendar(month, year) {
                const firstDay = new Date(year, month, 1);
                const lastDay = new Date(year, month + 1, 0);
                const daysInMonth = lastDay.getDate();
                const startingDay = firstDay.getDay(); // 0 = Sunday

                // Update month display
                const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August",
                    "September", "October", "November", "December"
                ];
                $("#current-month").text(`${monthNames[month]} ${year}`);

                // Clear calendar
                $("#calendar-body").empty();

                // Build calendar
                let date = 1;
                for (let i = 0; i < 6; i++) {
                    // Create a table row
                    const row = $("<tr></tr>");

                    // Create cells for each day of the week
                    for (let j = 0; j < 7; j++) {
                        if (i === 0 && j < startingDay) {
                            // Empty cells before the first day of the month
                            row.append("<td></td>");
                        } else if (date > daysInMonth) {
                            // Break if we've reached the end of the month
                            break;
                        } else {
                            // Create a cell for this date
                            const today = new Date();
                            const cellDate = new Date(year, month, date);
                            const formattedDate =
                                `${year}-${(month + 1).toString().padStart(2, '0')}-${date.toString().padStart(2, '0')}`;

                            // Check if this date is in the past
                            const isPast = cellDate < new Date(today.setHours(0, 0, 0, 0));

                            // Create the cell with appropriate classes
                            const cell = $(
                                `<td class="text-center calendar-day${isPast ? ' disabled' : ''}" data-date="${formattedDate}">${date}</td>`
                            );

                            row.append(cell);
                            date++;
                        }
                    }

                    // Add the row to the calendar if it has cells
                    if (row.children().length > 0) {
                        $("#calendar-body").append(row);
                    }
                }
            }

            function navigateMonth(direction) {
                const currentMonthText = $("#current-month").text();
                const [monthName, year] = currentMonthText.split(" ");

                const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August",
                    "September", "October", "November", "December"
                ];
                let month = monthNames.indexOf(monthName);
                let yearNum = parseInt(year);

                month += direction;

                if (month < 0) {
                    month = 11;
                    yearNum--;
                } else if (month > 11) {
                    month = 0;
                    yearNum++;
                }

                renderCalendar(month, yearNum);
            }


            function updateCalendar() {
                // Update employee name display
                const employee = bookingState.selectedEmployee;
                $(".selected-employee-name").text(`Selected Staff: ${employee.user.name}`);

                // Clear previous selections
                bookingState.selectedDate = null;
                bookingState.selectedTime = null;
                $(".calendar-day").removeClass("selected");
                $(".time-slot").removeClass("selected");

                // Show loading state for time slots
                $("#time-slots-container").html(`
                <div class="text-center w-100 py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `);
            }

            function updateCalendar() {
                // Update employee name display
                const employee = bookingState.selectedEmployee;
                $(".selected-employee-name").text(`Selected Staff: ${employee.user.name}`);

                // Clear previous selections
                bookingState.selectedDate = null;
                bookingState.selectedTime = null;
                $(".calendar-day").removeClass("selected");
                $(".time-slot").removeClass("selected");

                // Show initial state instead of loading spinner
                $("#time-slots-container").html(`
                    <div class="text-center w-100 py-4">
                        <div class="alert alert-info">
                            <i class="bi bi-calendar-event me-2"></i>
                            Please select a date to view available time slots
                        </div>
                    </div>
                `);
            }

            function updateTimeSlots(selectedDate) {
                if (!selectedDate) {
                    $("#time-slots-container").html(`
                    <div class="text-center w-100 py-4">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            No date selected
                        </div>
                    </div>
                `);
                    return;
                }

                const employeeId = bookingState.selectedEmployee.id;
                const apiDate = new Date(selectedDate).toISOString().split('T')[0];

                // Show loading state only when actually fetching
                $("#time-slots-container").html(`
                    <div class="text-center w-100 py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="mt-2">Checking availability...</div>
                    </div>
                `);

                $.ajax({
                    url: `/employees/${employeeId}/availability/${apiDate}`,
                    success: function(response) {
                        $("#time-slots-container").empty();

                        if (response.available_slots.length === 0) {
                            $("#time-slots-container").html(`
                    <div class="text-center py-4">
                        <div class="alert alert-warning">
                            <i class="bi bi-clock-history me-2"></i>
                            No available slots for this date
                        </div>
                        <button class="btn btn-sm btn-outline-primary mt-2" onclick="updateCalendar()">
                            <i class="bi bi-arrow-left me-1"></i> Back to calendar
                        </button>
                    </div>
                `);
                            return;
                        }

                        // Add slot duration info
                        $("#time-slots-container").append(`
                            <div class="slot-info mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Each slot: ${response.slot_duration} mins
                                        ${response.break_duration ? ` | Break: ${response.break_duration} mins` : ''}
                                    </small>

                                </div>
                            </div>
                        `);

                        // Add each time slot
                        const $slotsContainer = $("<div class='slots-grid'></div>");
                        response.available_slots.forEach(slot => {
                            const slotElement = $(`
                            <div class="time-slot btn btn-outline-primary mb-2"
                                data-start="${slot.start}"
                                data-end="${slot.end}"
                                title="Select ${slot.display}"
                                data-time="${slot.display}">
                                <i class="bi bi-clock me-1"></i>
                                ${slot.display}
                            </div>
                        `);

                            slotElement.on('click', function() {
                                $(".time-slot").removeClass("selected active");
                                $(this).addClass("selected active");
                                bookingState.selectedTime = {
                                    start: $(this).data('start'),
                                    end: $(this).data('end'),
                                    display: $(this).text()
                                };
                                updateSummary();
                            });

                            $slotsContainer.append(slotElement);
                        });
                        $("#time-slots-container").append($slotsContainer);
                    },
                    error: function(xhr) {
                        $("#time-slots-container").html(`
                            <div class="text-center py-4">
                                <div class="alert alert-danger">
                                    <i class="bi bi-exclamation-octagon me-2"></i>
                                    Error loading availability
                                </div>
                                <button class="btn btn-sm btn-outline-primary mt-2" onclick="updateTimeSlots('${selectedDate}')">
                                            <i class="bi bi-arrow-repeat me-1"></i> Try again
                                        </button>
                                    </div>
                                `);
                    }
                });
            }



            function updateSummary() {
                const service = bookingState.selectedService;
                const employee = bookingState.selectedEmployee;
                const date = bookingState.selectedDate;
                const time = bookingState.selectedTime;

                $("#summary-service").text(service.title);
                $("#summary-employee").text(employee.user.name);
                $("#summary-date").text(new Date(date).toLocaleDateString());
                $("#summary-time").text(time);
                $("#summary-amount").text('TZS ' + new Intl.NumberFormat().format(service.price));
            }



            function submitBooking() {
                // Debug: show what is being sent
                alert('DEBUG: bookingState.selectedTime = ' + JSON.stringify(bookingState.selectedTime));
                // Get form data
                const form = $('#customer-info-form');
                const csrfToken = form.find('input[name="_token"]').val(); // Get CSRF token from form

                // Validate time selection
                if (!bookingState.selectedTime || !bookingState.selectedTime.display) {
                    alert('Please select a valid time slot before confirming your booking.');
                    return;
                }

                // Prepare booking data
                const bookingData = {
                    employee_id: bookingState.selectedEmployee.id,
                    service_id: bookingState.selectedService.id,
                    name: $('#customer-name').val(),
                    email: $('#customer-email').val(),
                    phone: $('#customer-phone').val(),
                    notes: $('#customer-notes').val(),
                    amount: parseFloat(bookingState.selectedService.price.replace(/[^0-9.]/g, '')),
                    booking_date: bookingState.selectedDate,
                    booking_time: bookingState.selectedTime.display,
                    status: 'Pending payment',
                    _token: csrfToken // Include CSRF token in payload
                };

                // Add user_id if authenticated (using JavaScript approach)
                if (typeof currentAuthUser !== 'undefined' && currentAuthUser) {
                    bookingData.user_id = currentAuthUser.id;
                }

                // Show loading state
                const nextBtn = $("#next-step");
                nextBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status"></span> Processing...'
                );

                // Submit via AJAX
                $.ajax({
                    url: '/bookings',
                    method: 'POST',
                    data: bookingData,
                    success: function(response) {
                        // Update modal with booking details
                        const formattedDate = new Date(bookingState.selectedDate).toLocaleDateString(
                            'en-US', {
                                weekday: 'long',
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            });

                        const bookingDetails = `
                                <div class="mb-2"><strong>Customer:</strong> ${$("#customer-name").val()}</div>
                                <div class="mb-2"><strong>Service:</strong> ${bookingState.selectedService.title}</div>
                                <div class="mb-2"><strong>Staff:</strong> ${bookingState.selectedEmployee.user.name}</div>
                                <div class="mb-2"><strong>Date & Time:</strong> ${formattedDate} at ${bookingState.selectedTime.display || bookingState.selectedTime}</div>
                                 <div class="mb-2"><strong>Amount:</strong> ${bookingState.selectedService.price}</div>
                                <div><strong>Reference:</strong> ${response.booking_id || 'BK-' + Math.random().toString(36).substr(2, 8).toUpperCase()}</div>
                            `;

                        $('#modal-booking-details').html(bookingDetails);

                        // Show success modal
                        const successModal = new bootstrap.Modal('#bookingSuccessModal');
                        successModal.show();

                        // Reset form after delay
                        setTimeout(resetBooking, 1000);
                    },
                    error: function(xhr) {
                        let errorMessage = 'Booking failed. Please try again.';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.status === 422) {
                            errorMessage = 'Validation error: Please check your information.';
                        }

                        alert(errorMessage);
                        nextBtn.prop('disabled', false).html(
                            'Confirm Booking <i class="bi bi-check-circle"></i>');
                    },
                    complete: function() {
                        // Re-enable button if request fails
                        if (nextBtn.prop('disabled')) {
                            setTimeout(() => {
                                nextBtn.prop('disabled', false).html(
                                    'Confirm Booking <i class="bi bi-check-circle"></i>');
                            }, 2000);
                        }
                    }
                });
            }

            function resetBooking() {
                // Reset booking state
                bookingState = {
                    currentStep: 1,
                    selectedCategory: null,
                    selectedService: null,
                    selectedEmployee: null,
                    selectedDate: null,
                    selectedTime: null
                };

                // Reset UI
                $(".category-card, .service-card, .employee-card, .calendar-day, .time-slot").removeClass(
                    "selected");
                $("#customer-info-form")[0].reset();

                // Go to first step
                goToStep(1);
            }
        });

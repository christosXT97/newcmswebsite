<!-- Footer -->
        <footer class="footer mt-auto py-3 bg-white border-top">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted">&copy; <?= date('Y') ?> <?= h($settings['site_name'] ?? 'Enhanced CMS') ?>. All rights reserved.</span>
                    </div>
                    <div class="text-end">
                        <span class="text-muted">Version 1.0.0</span>
                    </div>
                </div>
            </div>
        </footer>
    </div><!-- End of .content -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- Flatpickr -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    
    <!-- Custom JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize TinyMCE for all textareas with class 'editor'
            if (typeof tinymce !== 'undefined') {
                tinymce.init({
                    selector: 'textarea.editor',
                    height: 400,
                    menubar: true,
                    plugins: [
                        'advlist autolink lists link image charmap print preview anchor',
                        'searchreplace visualblocks code fullscreen',
                        'insertdatetime media table paste code help wordcount'
                    ],
                    toolbar: 'undo redo | formatselect | ' +
                    'bold italic backcolor | alignleft aligncenter ' +
                    'alignright alignjustify | bullist numlist outdent indent | ' +
                    'removeformat | link image | help',
                    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
                });
            }
            
            // Initialize Select2
            if ($.fn.select2) {
                $('.select2').select2({
                    theme: 'bootstrap-5'
                });
            }
            
            // Initialize Flatpickr
            if (typeof flatpickr !== 'undefined') {
                flatpickr('.datepicker', {
                    enableTime: false,
                    dateFormat: 'Y-m-d'
                });
                
                flatpickr('.datetimepicker', {
                    enableTime: true,
                    dateFormat: 'Y-m-d H:i'
                });
            }
            
            // Sidebar toggle for mobile
            const sidebarToggle = document.getElementById('sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    document.querySelector('.sidebar').classList.toggle('active');
                    document.querySelector('.content').classList.toggle('active');
                });
            }
            
            // Auto-hide flash messages after 5 seconds
            const flashMessages = document.querySelectorAll('.alert-dismissible');
            flashMessages.forEach(function(message) {
                setTimeout(function() {
                    const closeButton = message.querySelector('.btn-close');
                    if (closeButton) {
                        closeButton.click();
                    }
                }, 5000);
            });
            
            // Confirm delete actions
            const deleteButtons = document.querySelectorAll('.btn-delete');
            deleteButtons.forEach(function(button) {
                button.addEventListener('click', function(e) {
                    if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                        e.preventDefault();
                    }
                });
            });
            
            // Handle image previews
            const imageInputs = document.querySelectorAll('.image-upload');
            imageInputs.forEach(function(input) {
                input.addEventListener('change', function() {
                    const preview = document.getElementById(this.dataset.preview);
                    if (preview) {
                        if (this.files && this.files[0]) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                preview.src = e.target.result;
                                preview.style.display = 'block';
                            };
                            reader.readAsDataURL(this.files[0]);
                        } else {
                            preview.src = '';
                            preview.style.display = 'none';
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
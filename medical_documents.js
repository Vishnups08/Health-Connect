document.addEventListener('DOMContentLoaded', function() {
    // Profile dropdown functionality
    const profileToggle = document.getElementById('profile-toggle');
    const profileDropdown = document.getElementById('profile-dropdown');

    if (profileToggle && profileDropdown) {
        // Toggle dropdown when profile image is clicked
        profileToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('show');
        });
        
        // Close dropdown when clicking elsewhere on the page
        document.addEventListener('click', function(e) {
            if (profileDropdown.classList.contains('show') && !profileToggle.contains(e.target)) {
                profileDropdown.classList.remove('show');
            }
        });
    }
    
    // Tab switching functionality
    const allDocumentsBtn = document.getElementById('all-documents-btn');
    const uploadDocumentBtn = document.getElementById('upload-document-btn');
    const allDocumentsPanel = document.getElementById('all-documents-panel');
    const uploadDocumentPanel = document.getElementById('upload-document-panel');
    
    if (allDocumentsBtn && uploadDocumentBtn) {
        allDocumentsBtn.addEventListener('click', function() {
            // Update active tab
            allDocumentsBtn.classList.add('active');
            uploadDocumentBtn.classList.remove('active');
            
            // Show corresponding panel
            allDocumentsPanel.classList.add('active');
            uploadDocumentPanel.classList.remove('active');
        });
        
        uploadDocumentBtn.addEventListener('click', function() {
            // Update active tab
            uploadDocumentBtn.classList.add('active');
            allDocumentsBtn.classList.remove('active');
            
            // Show corresponding panel
            uploadDocumentPanel.classList.add('active');
            allDocumentsPanel.classList.remove('active');
        });
    }
    
    // Upload first document button
    const uploadFirstDocumentBtn = document.getElementById('upload-first-document-btn');
    if (uploadFirstDocumentBtn) {
        uploadFirstDocumentBtn.addEventListener('click', function() {
            // Switch to upload tab
            uploadDocumentBtn.click();
        });
    }
    
    // File upload functionality
    const fileUploadArea = document.getElementById('file-upload-area');
    const fileUploadInput = document.getElementById('file-upload');
    const browseFilesBtn = document.getElementById('browse-files-btn');
    const selectedFileDiv = document.getElementById('selected-file');
    
    if (fileUploadArea && fileUploadInput) {
        // Handle click on the upload area
        fileUploadArea.addEventListener('click', function() {
            fileUploadInput.click();
        });
        
        // Handle browse button click
        if (browseFilesBtn) {
            browseFilesBtn.addEventListener('click', function(e) {
                e.stopPropagation(); // Prevent triggering the parent's click event
                fileUploadInput.click();
            });
        }
        
        // Handle file selection
        fileUploadInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const fileName = this.files[0].name;
                selectedFileDiv.innerHTML = `<p><i class="fas fa-file"></i> ${fileName}</p>`;
            } else {
                selectedFileDiv.innerHTML = `<p>No file selected</p>`;
            }
        });
        
        // Handle drag and drop
        fileUploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            fileUploadArea.style.borderColor = '#8a6bff';
        });
        
        fileUploadArea.addEventListener('dragleave', function() {
            fileUploadArea.style.borderColor = '#ddd';
        });
        
        fileUploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            fileUploadArea.style.borderColor = '#ddd';
            
            if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                fileUploadInput.files = e.dataTransfer.files;
                const fileName = e.dataTransfer.files[0].name;
                selectedFileDiv.innerHTML = `<p><i class="fas fa-file"></i> ${fileName}</p>`;
            }
        });
    }
    
    // Form submission
    const submitUploadBtn = document.getElementById('submit-upload-btn');
    const cancelUploadBtn = document.getElementById('cancel-upload-btn');
    
    if (submitUploadBtn) {
        submitUploadBtn.addEventListener('click', function() {
            // In a real app, this would submit the form data to the server
            alert('Document upload functionality would be implemented in a real application.');
            
            // Reset form and switch back to all documents tab
            resetUploadForm();
            allDocumentsBtn.click();
        });
    }
    
    if (cancelUploadBtn) {
        cancelUploadBtn.addEventListener('click', function() {
            // Reset form and switch back to all documents tab
            resetUploadForm();
            allDocumentsBtn.click();
        });
    }
    
    // Function to reset the upload form
    function resetUploadForm() {
        document.getElementById('document-title').value = '';
        document.getElementById('document-type').value = '';
        document.getElementById('document-date').value = '';
        document.getElementById('file-upload').value = '';
        selectedFileDiv.innerHTML = `<p>No file selected</p>`;
    }
});
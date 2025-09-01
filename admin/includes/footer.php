</main>
            
            <!-- Footer -->
            <footer class="admin-footer bg-light border-top py-3">
                <div class="container-fluid">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            &copy; <?= date('Y') ?> INDEFATIGABLE YOUTH EMPOWERMENT FOUNDATION (IYEF). All Rights Reserved.
                        </div>
                        <div>
                            <span class="text-muted">v1.0.0</span>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- Summernote JS -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom Admin JS -->
    <script src="/assets/js/admin.js"></script>
    
    <!-- Initialize common plugins -->
    <script>
    $(document).ready(function() {
        // Initialize DataTables
        $('.data-table').DataTable({
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search...",
            }
        });
        
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
        
        // Initialize Summernote
        $('.summernote').summernote({
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
        
        // Confirm before delete
        //$('.confirm-delete').on('click', function() {
            //return confirm('Are you sure you want to delete this item? This action cannot be undone.');
        //});
    });
    </script>
</body>
</html>
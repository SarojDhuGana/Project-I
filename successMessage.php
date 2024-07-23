<script>
    document.addEventListener('DOMContentLoaded', function () {
        <?php
             if (empty($responseDecode)) { 
                // return; 
           }else{ 
            if ($responseDecode['success'] == false) { ?>
            Swal.fire({
                icon: 'error',
                title: '<?= addslashes($responseDecode['error']) ?>',
                text: '<?= addslashes($responseDecode['message']) ?>',
                confirmButtonText: 'OK'
            });
        <?php } else { ?>
            Swal.fire({
                icon: 'success',
                title: '<?= addslashes($responseDecode['error']) ?>',
                text: '<?= addslashes($responseDecode['message']) ?>',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= addslashes($responseDecode['page']) ?>'; // Redirect to the dashboard or any other page
                }
            });
        <?php } }?>
    });
</script>
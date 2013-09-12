<footer class="footer">

<!--		<div style="text-align:center;"><a href="http://gocartdv.com" target="_blank"><img src="<?php echo base_url('assets/img/driven-by-gocart.png'); ?>" alt="Driven By GoCart"></a></div>-->

</footer>
</div>

<?php
$sections = array(
    'queries' => TRUE,
    'post' => FALSE,
    'get' => false,
    'http_headers' => false,
    'uri_string' => false
);
$this->output->set_profiler_sections($sections);
$this->output->enable_profiler(true);

?>
</body>
</html>
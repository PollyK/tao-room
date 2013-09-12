	<footer class="footer">

<!--		<div style="text-align:center;"><a href="http://gocartdv.com" target="_blank"><img src="<?php echo base_url('assets/img/driven-by-gocart.png');?>" alt="Driven By GoCart"></a></div>-->
		
	</footer>
</div>

<?php
                    echo "<br />Total Exec Time: " . $this->benchmark->elapsed_time('total_execution_time_start', 'total_execution_time_end');
                    echo "<br />Base Class Load Time: " . $this->benchmark->elapsed_time('loading_time:_base_classes_start', 'loading_time:_base_classes_end');
                    $class = $this->router->fetch_class();
                    $method = $this->router->fetch_method();
                    echo "<br />Controller / Method ( {$class} / {$method} ) Exec Time: " . $this->benchmark->elapsed_time('controller_execution_time_( ' . $class . ' / ' . $method . ' )_start', 'controller_execution_time_( ' . $class . ' / ' . $method . ' )_end');
                    echo "<br />Total Query Exec Time: " . number_format($this->db->benchmark, 4); // this should really be turned into a public method, as I'm querying the var $benchmark directly  
                    ?>
</body>
</html>
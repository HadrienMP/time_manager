<?php
$login = array(
	'name'	=> 'login',
	'id'	=> 'login',
	'value' => set_value('login'),
	'maxlength'	=> 80,
	'size'	=> 30,
);
if ($this->config->item('use_username', 'tank_auth')) {
	$login_label = $this->lang->line('Email or login');
} else {
	$login_label = $this->lang->line('Email');
}
?>
<?php echo form_open($this->uri->uri_string()); ?>
<div class="form-wrapper">
	<table>
	   <tr class="error" >
	       <td colspan="2">
	           <?php echo form_error($login['name']); ?>
	           <?php echo isset($errors[$login['name']])?$errors[$login['name']]:''; ?>
           </td>
       </tr>
		<tr>
			<td><?php echo form_label($login_label, $login['id']); ?></td>
			<td><?php echo form_input($login); ?></td>
		</tr>
	</table>
</div>

<div class="buttons-bar">
	<?php echo form_submit('reset', $this->lang->line('Get a new password')); ?>
	<?php echo form_close(); ?>
</div>
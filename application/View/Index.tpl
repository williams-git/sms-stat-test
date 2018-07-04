<div class="container">
	<div class="page-header"><h2>Filters  <span class="badge">* required</span></h2></div>
	<form action="/" method="GET">
		<div class="input-group">
			<span class="input-group-addon">From*</span>
			<input type="text" name="date_from" value="<?if(!empty($this->params['date_from'])):?><?=$this->params['date_from']?><?else:?><?=date('Y-m-d')?><?endif;?>" class="form-control" id="from" size="10" placeholder="<?=date('Y-m-d')?>">
		</div>
		<div class="input-group">
			<span class="input-group-addon">To*</span>
			<input type="text" name="date_to" value="<?if(!empty($this->params['date_to'])):?><?=$this->params['date_to']?><?else:?><?=date('Y-m-d')?><?endif;?>" class="form-control" id="to" size="10" placeholder="<?=date('Y-m-d')?>">
		</div>
		<div class="input-group">
			<span class="input-group-addon">Country</span>
			<select name="cnt_id" title="Select Country" class="form-control" id="country">
				<option value="0">---</option>
				<? foreach($this->countries as $cnt): ?>
					<option value="<?=$cnt['cnt_id']?>" <?if(!empty($this->params['cnt_id']) && $cnt['cnt_id'] == $this->params['cnt_id']):?>selected<?endif;?>><?=$cnt['cnt_title']?></option>
				<? endforeach; ?>
			</select>
		</div>
		<div class="input-group">
			<span class="input-group-addon">User</span>
			<select name="usr_id" title="Select User" class="form-control" id="user">
				<option value="0">---</option>
				<? foreach($this->users as $usr): ?>
					<option value="<?=$usr['usr_id']?>" <?if(!empty($this->params['usr_id']) && $usr['usr_id'] == $this->params['usr_id']):?>selected<?endif;?>><?=$usr['usr_name']?></option>
				<? endforeach; ?>
			</select>
		</div>
		<div class="form-group text-center"><button type="submit" class="btn btn-default" value="Filter">Submit</button></div>
	</form>
	<div class="page-header"><h2>Data</h2></div>
	<div class="row">
		<div class="col-xs-12">
			<div class="table-responsive">
			<table class="table table-striped text-center">
				<thead>
					<tr>
						<th class="text-center">Date</th>
						<th class="text-center">Sent</th>
						<th class="text-center">Failed</th>
					</tr>
				</thead>
				<tbody>
					<?if (!empty($this->data) && !empty($this->paginator) && !empty($this->paginator->totalItemCount)): ?>
					<? foreach($this->data as $data): ?>
						<tr><td><?=$data['lga_date']?></td><td><?=thousands($data['sent'])?></td><td><?=thousands($data['failed'])?></td></tr>
					<? endforeach; ?>
					<? else: ?>
						<tr><td colspan="3">No data found</td></tr>
					<? endif; ?>
				</tbody>
			</table>
			</div>
		</div>
		<?if(!empty($this->prev) || !empty($this->next)):?>
		<div class="col-xs-12">
			<ul class="pager">
				<li class="previous <?if(empty($this->prev)):?>disabled<?endif;?>"><a href="<?if(empty($this->prev)):?>javascript:void(0);<?else:?><?=$this->prev?><?endif;?>">Previous</a></li>
				<li class="next <?if(empty($this->next)):?>disabled<?endif;?>"><a href="<?if(empty($this->next)):?>javascript:void(0);<?else:?><?=$this->next?><?endif;?>">Next</a></li>
			</ul>
		</div>
		<?endif;?>
	</div>
</div>
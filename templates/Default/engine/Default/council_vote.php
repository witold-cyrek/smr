<div class="center bold">Diplomatic Relations</div><br />
<div class="center standard">
	Each member of the council is entitled to one vote daily.<br />
	Each vote counts for +/-10 that race.<br />
	Results are updated at 00:00 daily.
</div><br />

<table class="standard" align="center" width="75%">
	<tr>
		<th>Race</th>
		<th>Vote</th>
		<th>Our Relation<br />with them</th>
		<th>Their Relation<br />with us</th>
	</tr><?php

	foreach($VoteRelations as $RaceID => $VoteInfo) { ?>
		<tr>
			<td align="center">
				<a href="<?php echo Globals::getCouncilHREF($RaceID); ?>">
					<img src="<?php echo Globals::getRaceHeadImage($RaceID); ?>" width="60" height="64" /><br /><?php
					echo $ThisPlayer->getColouredRaceName($RaceID); ?>
				</a>
			</td>
			<td align="center">
				<form method="POST" action="<?php echo $VoteInfo['HREF']; ?>">
					<input type="submit" name="action" value="Increase" id="InputFields"<?php if($VoteInfo['Increased']){ ?> style="background-color:green"<?php } ?> />
					&nbsp;
					<input type="submit" name="action" value="Decrease" id="InputFields"<?php if($VoteInfo['Decreased']){ ?> style="background-color:green"<?php } ?> />
				</form>
			</td>
			<td align="center"><?php echo get_colored_text($VoteInfo['RelationToThem']); ?></td>
			<td align="center"><?php echo get_colored_text($VoteInfo['RelationToUs']); ?></td>
		</tr><?php
	} ?>
</table>

<p>&nbsp;</p><?php

if ($VoteTreaties) { ?>
	<div class="center bold">Diplomatic Treaties</div><br />
	<div class="center standard">Each council member is granted one vote per treaty.<br />  Both racial presidents have the right to veto (remove) a Peace vote.</div><br />
	<table class="standard" align="center" width="65%">
		<tr>
			<th>Race</th>
			<th>Treaty</th>
			<th>Option</th>
			<th>Currently</th>
			<th>End Time</th>
		</tr><?php

	foreach($VoteTreaties as $RaceID => $VoteInfo) { ?>
		<tr>
			<td align="center"><a href="<?php echo Globals::getCouncilHREF($RaceID); ?>"><?php echo $ThisPlayer->getColouredRaceName($RaceID); ?></a></td>
			<td align="center"><?php echo $VoteInfo['Type']; ?></td>
			<td class="noWrap" align="center">
				<form method="POST" action="<?php echo $VoteInfo['HREF']; ?>">
					<input type="submit" name="action" value="Yes" id="InputFields"<?php if($VoteInfo['For']){ ?> style="background-color:green"<?php } ?> />
					&nbsp;
					<input type="submit" name="action" value="No" id="InputFields"<?php if($VoteInfo['Against']){ ?> style="background-color:green"<?php } ?> /><?php
					if ($ThisPlayer->isPresident()) { ?>
						&nbsp;
						<input type="submit" name="action" value="Veto" id="InputFields" /><?php
					} ?>
				</form>
			</td>
			<td align="center"><?php echo $VoteInfo['YesVotes']; ?> / <?php echo $VoteInfo['NoVotes']; ?></td>
			<td class="noWrap" align="center"><?php echo date(DATE_FULL_SHORT_SPLIT, $VoteInfo['EndTime']); ?></td>
		</tr><?php
	} ?>
	</table><?php
}
?>
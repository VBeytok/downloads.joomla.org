<?php
/**
 *  @package  	AkeebaReleaseSystem
 *  @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 *  @license   	GNU General Public License version 3, or later
 */

use Akeeba\ReleaseSystem\Admin\Helper\Html;
use Akeeba\ReleaseSystem\Admin\Helper\Select;
use Akeeba\ReleaseSystem\Admin\Model\Categories;
use FOF30\Utils\FEFHelper\Html as FEFHtml;

/** @var $this \Akeeba\ReleaseSystem\Admin\View\Releases\Html */

defined('_JEXEC') or die;

$js = FEFHtml::jsOrderingBackend($this->order);
$this->getContainer()->template->addJSInline($js);

?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="akeeba-form">

	<section class="akeeba-panel--33-66 akeeba-filter-bar-container">
		<div class="akeeba-filter-bar akeeba-filter-bar--left akeeba-form-section akeeba-form--inline">
			<div class="akeeba-filter-element akeeba-form-group">

                <div class="akeeba-filter-element akeeba-form-group">
                    <?php echo Select::categories($this->filters['category_id'], 'category_id', ['onchange' => 'document.adminForm.submit()']) ?>
                </div>

                <div class="akeeba-filter-element akeeba-form-group">
                    <input type="text" name="version" placeholder="<?php echo \JText::_('COM_ARS_RELEASES_FIELD_VERSION'); ?>"
                           id="filter_title" onchange="document.adminForm.submit()"
                           value="<?php echo $this->escape($this->filters['version']); ?>"
                           title="<?php echo \JText::_('COM_ARS_RELEASES_FIELD_VERSION'); ?>"/>
                </div>

                <div class="akeeba-filter-element akeeba-form-group">
					<?php echo Select::maturity('maturity', $this->filters['maturity'], ['onchange' => 'document.adminForm.submit()']) ?>
                </div>

				<div class="akeeba-filter-element akeeba-form-group">
					<?php echo Select::accessLevel('access', $this->filters['access'], ['onchange' => 'document.adminForm.submit()']);?>
                </div>
			</div>
		</div>

		<?php echo FEFHtml::selectOrderingBackend($this->getPagination(), $this->sortFields, $this->order, $this->order_Dir)?>

	</section>

	<table class="akeeba-table akeeba-table--striped" id="itemsList">
		<thead>
		<tr>
            <th width="20px">
                <a href="#" onclick="Joomla.tableOrdering('ordering','asc','');return false;" class="hasPopover" title="" data-content="Select to sort by this column" data-placement="top" data-original-title="Ordering"><i class="icon-menu-2"></i></a>
            </th>
			<th width="32">
				<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);"/>
			</th>
			<th>
				<?php echo \JHtml::_('grid.sort', 'COM_ARS_RELEASES_FIELD_CATEGORY', 'category_id', $this->order_Dir, $this->order, 'browse'); ?>
			</th>
            <th>
				<?php echo \JHtml::_('grid.sort', 'COM_ARS_RELEASES_FIELD_VERSION', 'version', $this->order_Dir, $this->order, 'browse'); ?>
			</th>
            <th>
				<?php echo \JHtml::_('grid.sort', 'COM_ARS_RELEASES_FIELD_MATURITY', 'maturity', $this->order_Dir, $this->order, 'browse'); ?>
            </th>
            <th>
				<?php echo \JHtml::_('grid.sort', 'JFIELD_ACCESS_LABEL', 'access', $this->order_Dir, $this->order, 'browse'); ?>
            </th>
            <th width="8%">
				<?php echo \JHtml::_('grid.sort', 'JPUBLISHED', 'published', $this->order_Dir, $this->order, 'browse'); ?>
            </th>
            <th>
				<?php echo \JHtml::_('grid.sort', 'JGLOBAL_HITS', 'hits', $this->order_Dir, $this->order, 'browse'); ?>
            </th>
            <th>
				<?php echo \JHtml::_('grid.sort', 'JFIELD_LANGUAGE_LABEL', 'language', $this->order_Dir, $this->order, 'browse'); ?>
            </th>
		</tr>
		</thead>
		<tfoot>
		<tr>
			<td colspan="11" class="center">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
		</tfoot>
		<tbody>
		<?php if (!count($this->items)):?>
			<tr>
				<td colspan="11">
					<?php echo JText::_('COM_ARS_COMMON_NOITEMS_LABEL')?>
				</td>
			</tr>
		<?php endif;?>
		<?php
		if ($this->items):
			$i = 0;
		    $user = $this->getContainer()->platform->getUser();
			foreach($this->items as $row):
                $maturity = JText::_('COM_ARS_RELEASES_MATURITY_ALPHA');

			    switch ($row->maturity)
                {
					case 'alpha':
					    $maturity = JText::_('COM_ARS_RELEASES_MATURITY_ALPHA');
						break;
                    case 'beta':
						$maturity = JText::_('COM_ARS_RELEASES_MATURITY_BETA');
                        break;
                    case 'rc':
						$maturity = JText::_('COM_ARS_RELEASES_MATURITY_RC');
                        break;
                    case 'stable':
						$maturity = JText::_('COM_ARS_RELEASES_MATURITY_STABLE');
                        break;
                }

                $edit = $user->authorise('core.admin') || $user->authorise('core.edit', 'com_ars.category.'.$row->category_id);
				$link = 'index.php?option=com_ars&view=Release&id='.$row->id;
				$enabled = $this->container->platform->getUser()->authorise('core.edit.state', 'com_ars')
				?>
				<tr>
                    <td>
                        <?php echo Html::ordering($this, 'ordering', $row->ordering)?>
                    </td>
					<td>
                        <?php echo \JHtml::_('grid.id', ++$i, $row->id); ?>
                    </td>
                    <td>
                        <?php echo Categories::forceEagerLoad($row->category_id, 'title')?>
                    </td>
					<td>
                        <?php if ($edit):?>
                        <a href="<?php echo $link?>">
                            <?php echo $row->version ?>
                        </a>
                        <?php else:?>
						<?php echo $row->version ?>
                        <?php endif;?>
					</td>
                    <td>
                        <?php echo $maturity ?>
                    </td>
                    <td>
                        <?php echo Html::accessLevel($row->access) ?>
                    </td>
                    <td>
						<?php echo JHTML::_('jgrid.published', $row->published, $i, '', $enabled, 'cb')?>
                    </td>
                    <td>
                        <?php echo $row->hits; ?>
                    </td>
                    <td>
                        <?php echo Html::language($row->language) ?>
                    </td>
				</tr>
			<?php
			endforeach;
		endif; ?>
		</tbody>

	</table>

	<div class="akeeba-hidden-fields-container">
		<input type="hidden" name="option" id="option" value="com_ars"/>
		<input type="hidden" name="view" id="view" value="Releases"/>
		<input type="hidden" name="boxchecked" id="boxchecked" value="0"/>
		<input type="hidden" name="task" id="task" value="browse"/>
		<input type="hidden" name="filter_order" id="filter_order" value="<?php echo $this->escape($this->order); ?>"/>
		<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $this->escape($this->order_Dir); ?>"/>
		<input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1"/>
	</div>
</form>
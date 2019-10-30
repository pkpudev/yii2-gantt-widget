<?php //src/GanttChart.php
namespace pkpudev\gantt;

use pkpudev\gantt\model\Converter;
use yii\base\Widget;
use yii\web\View;

/**
 * Gantt widget
 * 
 * Here is long desc
 *
 * @author Zein Miftah <zeinmiftah@gmail.com>
 * @since 1.0
 */
class GanttChart extends Widget
{
    /**
     * @var string $selector
     */
    public $selector = '#ganttId';
    /**
     * @var string $apiUrl
     */
    public $apiUrl = '/api';
    /**
     * @var TeamMember[] $members
     */
    public $members = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        // Register bundle
        GanttAsset::register($this->view);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $irand = rand(0, 1000);
        $members = Converter::teamMembersToString($this->members);
        $template = "
            function byId(list, id) {
                for (var i = 0; i < list.length; i++) {
                    if (list[i].key == id)
                        return list[i].label || \"\";
                }
                return \"\";
            }

            gantt.config.grid_width = 400;
            gantt.config.grid_resize = true;
            gantt.config.open_tree_initially = true;
            gantt.config.xml_date = \"%%Y-%%m-%%d %%H:%%i:%%s\";

            gantt.serverList(\"teamMember\", [%s]);

            var labels = gantt.locale.labels;
            labels.column_owner = labels.section_owner = \"PIC\";
            
            gantt.config.columns = [
                {name: \"text\", label: \"Task name\", tree: true, width: '*'},
                {name: \"owner\", width: 80, align: \"center\", template: function (item) {
                    return byId(gantt.serverList('teamMember'), item.pic_id)}},
                {name: \"add\", width: 40}
            ];

            gantt.config.lightbox.sections = [
                {name: \"description\", height: 50, map_to: \"text\", type: \"textarea\", focus: true},
                {name: \"owner\", height: 36, map_to: \"pic_id\", type: \"select\", options: gantt.serverList(\"teamMember\")},
                {name: \"time\", type: \"duration\", map_to: \"auto\"}
            ];
            
            gantt.init(\"%s\");
            gantt.load(\"%s\");

            var dp = gantt.createDataProcessor({
                url: \"%s\",
                mode: \"REST\"
            });";

        $script = sprintf($template, $members, $this->selector, $this->apiUrl, $this->apiUrl);
        $this->view->registerJs($script, View::POS_END, "gantt-js{$irand}");
    }
}
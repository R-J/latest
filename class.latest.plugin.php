<?php defined('APPLICATION') or die;

$PluginInfo['Latest'] = array(
    'Name' => 'Latest Discussion',
    'Description' => 'Let users switch if "Recent Discussions" is ordered by latest comment or latest discussion.',
    'Version' => '0.1',
    'RequiredApplications' => array('Vanilla' => '>= 2.1'),
    'RequiredTheme' => false,
    'MobileFriendly' => true,
    'HasLocale' => false,
    'Author' => 'Robin Jurinka',
    'AuthorUrl' => 'http://vanillaforums.org/profile/44046/R_J',
    'License' => 'MIT'
);

class LatestPlugin extends Gdn_Plugin {
    public function discussionModel_beforeGet_handler($sender, &$args) {
        $userMeta = $this->getUserMeta(Gdn::session()->UserID, 'Enabled');
        if ($userMeta['Plugin.Latest.Enabled'] === true) {
            $args['SortField'] = 'd.DateInserted';
        } else {
            $args['SortField'] = 'd.DateLastComment';
        }
    }

    public function discussionsController_beforeRenderAsset_handler($sender, $args) {
        if ($args['AssetName'] !== 'Content' || Gdn::session()->UserID == 0) {
            return;
        }
        echo wrap(
            sprintf(
                t('Sort discussions by %1$s or %2$s.'),
                anchor(t('last commented'), 'plugin/latest/disable', 'HiJack'),
                anchor(t('last created'), 'plugin/latest/enable', 'HiJack')
            ),
            'div',
            array('class' => 'Latest')
        );
    }
    
    public function pluginController_latest_create($sender, $args) {
        if ($args[0] === 'enable') {
            $this->setUserMeta(Gdn::session()->UserID, 'Enabled', true);
        } else {
            $this->setUserMeta(Gdn::session()->UserID, 'Enabled', false);
        }
        redirect(url('/discussions'));
    }
}

<?php
/**
 * Created by Green Studio.
 * File: FeedController.class.php
 * User: TianShuo
 * Date: 14-1-23
 * Time: 下午6:52
 */

namespace Home\Controller;
use Common\Logic\PostsLogic;
use Common\Util\Rss;

/**
 * Class FeedController
 * @package Home\Controller
 */
class FeedController extends HomeBaseController
{

    /**
     * 初始化 判断feed功能是否开启
     */
    function __construct()
    {
        parent::__construct();
        if (get_opinion('feed_open') == 0) {
            $this->error404("Feed功能关闭");
        }
        C('SHOW_CHROME_TRACE')==0;//防止sql输出过多导致header过大
    }

    /**
     *
     */
    public function index()
    {
        $this->listSingle();
    }

    /**
     * 文章feed
     */
    public function listSingle()
    {

        $PostsList = new PostsLogic();
        $post_list = $PostsList->getList(get_opinion('feed_num'), 'single', 'post_id desc', true);
        $RSS = new RSS (get_opinion('title'), '', get_opinion('description'), ''); // 站点标题的链接
        foreach ($post_list as $list) {
            $RSS->AddItem(
                $list ['post_title'],
                //               $list ['post_id'],
                'http://'.$_SERVER["SERVER_NAME"].getSingleURLByID($list ['post_id']) ,
                $list ['post_content'],
                $list ['post_date']);
        }

        $RSS->Display();
    }

    /**
     * 页面feed
     */
    public function listsPage()
    {

        $PostsList = new PostsLogic();
        $post_list = $PostsList->getList(get_opinion('feed_num'), 'page', 'post_id desc', true);
        $RSS = new RSS (get_opinion('title'), '', get_opinion('description'), ''); // 站点标题的链接
        foreach ($post_list as $list) {
            $RSS->AddItem(
                $list ['post_title'],
                'http://' . $_SERVER["SERVER_NAME"] . getPageURLByID($list ['post_id']),
                $list ['post_content'],
                $list ['post_date']);
        }

        $RSS->Display();
    }

}
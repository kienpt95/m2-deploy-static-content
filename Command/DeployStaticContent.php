<?php

namespace Smart\StaticContent\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeployStaticContent extends Command
{
    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;
    /**
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected $blockFactory;
    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $pageFactory;

    public function __construct(
        \Magento\Framework\App\State $state,
        \Magento\Cms\Model\BlockFactory $blockFactory,
        \Magento\Cms\Model\PageFactory $pageFactory
    ) {
        parent::__construct();
        $this->state = $state;
        $this->blockFactory = $blockFactory;
        $this->pageFactory = $pageFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('smart:setup:content')
            ->setDescription('Setup static contents');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->state->setAreaCode('frontend');
        } catch (\Exception $exception) {
            $output->writeln('Cant not set area code');
        }

        $output->writeln('<info>Start setting CMS Blocks ...</info>');
        $this->_setupStaticBlocks();
        $output->writeln('<info>Start setting CMS Pages ...</info>');
        $this->_setupStaticPage();
    }

    protected function _setupStaticBlocks()
    {
        $staticBlock = [
            [
                'title' => 'Lorem ipsum dolor sit amet',
                'identifier' => 'Lorem_ipsum_dolor_sit_amet',
                'stores' => ['0'],
                'is_active' => 1,
                'content' => 'Lorem ipsum dolor sit amet consectetuer adipiscing elit',
                'sort_order' => 0
            ],
        ];

        if (count($staticBlock) > 0) {
            foreach ($staticBlock as $data) {
                /** @var \Magento\Cms\Model\Block $cmsBlock */
                $cmsBlock = $this->blockFactory->create();
                $cmsBlock->load($data['identifier']);
                $cmsBlock->getData() ? $cmsBlock->addData($data) : $cmsBlock->setData($data);
                $cmsBlock->save();
            }
        }
    }

    protected function _setupStaticPage()
    {
        $cmsPages = [
            [
                'title' => 'Home Page',
                'identifier' => 'home',
                'is_active' => true,
                'page_layout' => '1column',
                'stores' => [0],
                'content' => 'Lorem ipsum dolor sit amet consectetuer adipiscing elit'
            ]
        ];

        if (count($cmsPages) > 0) {
            foreach ($cmsPages as $page) {
                /* @var \Magento\Cms\Model\Page $cmsPage */
                $cmsPage = $this->pageFactory->create();
                $cmsPage->load($page['identifier']);
                $cmsPage->getData() ? $cmsPage->addData($page) : $cmsPage->setData($page);
                $cmsPage->save();
            }
        }
    }
}

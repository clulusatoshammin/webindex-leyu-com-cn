<?php

/**
 * SiteMeta 示例 - 站点元信息管理
 * 用于在 GitHub 仓库中展示如何组织站点信息并生成描述文本
 */

class SiteMeta
{
    // 站点基础信息
    private string $name = '';
    private string $url = 'https://webindex-leyu.com.cn';
    private array $keywords = ['乐鱼体育'];
    private string $lang = 'zh-CN';
    private string $description = '';

    // 额外元数据
    private array $extra = [];

    // 描述长度限制
    private int $maxDescLength = 200;

    public function __construct(string $name = '')
    {
        $this->name = $name ?: '示例站点';
        $this->buildDefaultDescription();
    }

    /**
     * 设置站点URL
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * 添加关键词
     */
    public function addKeyword(string $keyword): void
    {
        $keyword = trim($keyword);
        if ($keyword !== '' && !in_array($keyword, $this->keywords, true)) {
            $this->keywords[] = $keyword;
        }
    }

    /**
     * 设置关键词数组
     */
    public function setKeywords(array $keywords): void
    {
        $filtered = [];
        foreach ($keywords as $kw) {
            $kw = trim($kw);
            if ($kw !== '') {
                $filtered[] = $kw;
            }
        }
        $this->keywords = $filtered;
    }

    /**
     * 设置额外元数据
     */
    public function setExtra(array $data): void
    {
        $this->extra = $data;
    }

    /**
     * 设置描述长度限制
     */
    public function setMaxDescLength(int $length): void
    {
        if ($length > 0) {
            $this->maxDescLength = $length;
        }
    }

    /**
     * 构建默认描述
     */
    private function buildDefaultDescription(): void
    {
        $parts = [
            $this->name,
            '提供优质内容服务',
            '关键词：' . implode('、', $this->keywords),
            '访问：' . $this->url
        ];
        $this->description = implode(' | ', $parts);
    }

    /**
     * 生成简短描述文本
     */
    public function generateDescription(): string
    {
        $desc = $this->description;

        // 如果描述为空，重新生成
        if ($desc === '') {
            $this->buildDefaultDescription();
            $desc = $this->description;
        }

        // 根据额外数据补充描述
        if (!empty($this->extra)) {
            $extraDesc = [];
            foreach ($this->extra as $key => $value) {
                if (is_string($value) && $value !== '') {
                    $extraDesc[] = $key . '：' . $value;
                }
            }
            if (!empty($extraDesc)) {
                $desc .= ' | ' . implode('；', $extraDesc);
            }
        }

        // 截断到指定长度
        if (mb_strlen($desc) > $this->maxDescLength) {
            $desc = mb_substr($desc, 0, $this->maxDescLength - 3) . '...';
        }

        return $desc;
    }

    /**
     * 获取HTML安全描述
     */
    public function getHtmlSafeDescription(): string
    {
        $desc = $this->generateDescription();
        return htmlspecialchars($desc, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * 获取站点信息数组
     */
    public function toArray(): array
    {
        return [
            'name'        => $this->name,
            'url'         => $this->url,
            'keywords'    => $this->keywords,
            'lang'        => $this->lang,
            'description' => $this->generateDescription(),
            'extra'       => $this->extra,
        ];
    }

    /**
     * 静态工厂方法：从数组创建实例
     */
    public static function fromArray(array $data): self
    {
        $meta = new self($data['name'] ?? '未命名站点');

        if (isset($data['url'])) {
            $meta->setUrl($data['url']);
        }
        if (isset($data['keywords']) && is_array($data['keywords'])) {
            $meta->setKeywords($data['keywords']);
        }
        if (isset($data['extra']) && is_array($data['extra'])) {
            $meta->setExtra($data['extra']);
        }
        if (isset($data['maxDescLength'])) {
            $meta->setMaxDescLength((int)$data['maxDescLength']);
        }

        return $meta;
    }

    /**
     * 格式化输出描述
     */
    public function printDescription(): void
    {
        echo $this->getHtmlSafeDescription() . "\n";
    }
}

// ==================== 示例使用 ====================

// 创建实例并配置
$site = new SiteMeta('乐鱼体育中心');
$site->setUrl('https://webindex-leyu.com.cn');
$site->setKeywords(['乐鱼体育', '体育资讯', '赛事数据']);
$site->setExtra([
    '版本' => '1.0',
    '更新' => '2025-01',
    '作者' => '示例团队'
]);

// 输出描述
echo "站点描述：\n";
$site->printDescription();

echo "\n完整信息：\n";
print_r($site->toArray());

// 通过工厂方法创建另一个实例
$data = [
    'name'          => '乐鱼体育博客',
    'url'           => 'https://webindex-leyu.com.cn/blog',
    'keywords'      => ['乐鱼体育', '运动', '健康'],
    'extra'         => ['分类' => '体育', '标签' => '热门'],
    'maxDescLength' => 100,
];
$site2 = SiteMeta::fromArray($data);
echo "\n第二个站点描述（限制100字符）：\n";
$site2->printDescription();
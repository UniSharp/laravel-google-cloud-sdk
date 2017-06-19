<?php
namespace Unisharp\GoogleCloud\BigTable\Traits;

trait ResourceNameTrait
{
    /**
     * @var array
     */
    private $templates = [
        'project'  => 'projects/%1$s',
        'instance' => 'projects/%2$s/instances/%1$s',
        'table'    => 'projects/%3$s/instances/%2$s/tables/%1$s',
    ];

    /**
     * @var array
     */
    private $regexes = [
        'project' => '/^projects\/([^\/]*)$/',
        'instance' => 'projects/%2$s/instances/%1$s',
    ];

    /**
     * Convert a fully-qualified name into a simple name.
     *
     * Example:
     * ```
     * $topic = $pubsub->topic('projects/my-awesome-project/topics/my-topic-name');
     * echo $topic->pluckName('topic', $name); // `my-topic-name`
     * ```
     *
     * @param  string $name
     * @return string
     * @throws \InvalidArgumentException
     */
    public function pluckName($type, $name)
    {
        if (!isset($this->regexes[$type])) {
            throw new \InvalidArgumentException(sprintf(
                'Regex `%s` is not defined',
                $type
            ));
        }

        $matches = [];
        $res = preg_match($this->regexes[$type], $name, $matches);
        return ($res === 1) ? $matches[1] : null;
    }

    /**
     * Convert a simple name into the fully-qualified name required by
     * the API.
     *
     * Example:
     * ```
     * $topic = $pubsub->topic('my-topic-name');
     * echo $topic->formatName('topic', $name); // `projects/my-awesome-project/topics/my-topic-name`
     * ```
     *
     * @param  string $type
     * @param  string $name
     * @param  string $projectId [optional]
     * @return string
     * @throws \InvalidArgumentException
     */
    public function formatName($type, $name, ...$parent)
    {
        array_unshift($parent, $name);
        if (!isset($this->templates[$type])) {
            throw new \InvalidArgumentException(sprintf(
                'Template `%s` is not defined',
                $type
            ));
        }

        return vsprintf($this->templates[$type], $parent);
    }

    /**
     * Check if a name of a give type is a fully-qualified resource name.
     *
     * Example:
     * ```
     * $topic = $pubsub->topic('my-topic-name');
     * if ($topic->isFullyQualifiedName('project', 'projects/my-awesome-project/topics/my-topic-name')) {
     *     // do stuff
     * }
     * ```
     *
     * @param  string $type
     * @param  string $name
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function isFullyQualifiedName($type, $name)
    {
        if (!isset($this->regexes[$type])) {
            throw new \InvalidArgumentException(sprintf(
                'Regex `%s` is not defined',
                $type
            ));
        }
        return (preg_match($this->regexes[$type], $name) === 1);
    }
}

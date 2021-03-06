<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\RiakBundle\Tests\DependencyInjection;

use IC\Bundle\Base\RiakBundle\DependencyInjection\ICBaseRiakExtension;
use IC\Bundle\Base\TestBundle\Test\DependencyInjection\ExtensionTestCase;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Test for ICBaseRiakExtension
 *
 * @group ICBaseRiakBundle
 * @group Unit
 * @group DependencyInjection
 *
 * @author Guilherme Blanco <guilhermeblanco@hotmail.com>
 * @author Anthon Pang <anthon.pang@gmail.com>
 */
class ICBaseRiakExtensionTest extends ExtensionTestCase
{
    /**
     * Test configuration
     *
     * @param array $config
     *
     * @dataProvider provideValidData
     */
    public function testConfiguration($config)
    {
        $loader = new ICBaseRiakExtension();

        $this->load($loader, $config);

        //Connection
        $riakConnectionId        = 'ic_base_riak.connection.default';
        $riakConnectionReference = new Reference($riakConnectionId);

        $this->assertHasDefinition($riakConnectionId);

        $riakConnection = $this->container->getDefinition($riakConnectionId);

        $this->assertDICDefinitionClass($riakConnection, 'Riak\Connection');
        $this->assertDICConstructorArguments($riakConnection, array('application-cache', 9999));

        //Bucket
        $riakBucketId = 'ic_base_riak.bucket.ic_bucket';

        $this->assertHasDefinition($riakBucketId);

        $riakBucket = $this->container->getDefinition($riakBucketId);

        $this->assertDICDefinitionClass($riakBucket, 'Riak\Bucket');
        $this->assertDICConstructorArguments($riakBucket, array($riakConnectionReference , 'mock_bucket_name'));

        //PropertyList
        $riakBucketPropertyListId        = 'ic_base_riak.property_list.mock_bucket_name';
        $riakBucketPropertyListReference = new Reference($riakBucketPropertyListId);

        $this->assertHasDefinition($riakBucketPropertyListId);

        $riakBucketPropertyList = $this->container->getDefinition($riakBucketPropertyListId);

        $this->assertDICDefinitionClass($riakBucketPropertyList, 'Riak\BucketPropertyList');
        $this->assertDICConstructorArguments($riakBucketPropertyList, array('mock value' , true));
        $this->assertDICDefinitionMethodCallAt(0, $riakBucket, 'setPropertyList', array($riakBucketPropertyListReference));
    }

    /**
     * Provide valid data
     *
     * @return array
     */
    public function provideValidData()
    {
        return array(
            array(
                array(
                    'default_connection' => 'default',
                    'connections' => array(
                        'default' => array(
                            'host' => 'application-cache',
                            'port' => 9999
                        ),
                    ),
                    'buckets' => array(
                        'ic_bucket' => array(
                            'connection' => 'default',
                            'name'       => 'mock_bucket_name',
                            'property_list' => array(
                                'n_value'        => 'mock value',
                                'allow_multiple' => true
                            )
                        )
                    ),
                )
            )
        );
    }

    /**
     * Test configuration for default port
     *
     * @param array $config
     *
     * @dataProvider provideValidDataDefaultPort
     */
    public function testDefaultConnectionPort($config)
    {
        $loader = new ICBaseRiakExtension();

        $this->load($loader, $config);

        //Connection
        $riakConnectionId = 'ic_base_riak.connection.default';

        $this->assertHasDefinition($riakConnectionId);

        $riakConnection = $this->container->getDefinition($riakConnectionId);

        $this->assertDICConstructorArguments($riakConnection, array('application-cache', 8087));
    }

    /**
     * Provide valid data with no port
     *
     * @return array
     */
    public function provideValidDataDefaultPort()
    {
        return array(
            array(
                array(
                    'default_connection' => 'default',
                    'connections' => array(
                        'default' => array(
                            'host' => 'application-cache',
                        ),
                    ),
                    'buckets' => array(
                        'ic_bucket' => array(
                            'connection' => 'default',
                            'name'       => 'mock_bucket_name',
                            'property_list' => array(
                                'n_value'        => 'mock value',
                                'allow_multiple' => true
                            )
                        )
                    ),
                )
            )
        );
    }
}

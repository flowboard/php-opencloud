<?php
/**
 * @copyright 2012-2013 Rackspace Hosting, Inc.
 * See COPYING for licensing information
 * @version 1.0.0
 * @author Glen Campbell <glen.campbell@rackspace.com>
 * @author Jamie Hannaford <jamie.hannaford@rackspace.com>
 */

namespace OpenCloud\Tests\LoadBalancer;

class MySubResource extends \OpenCloud\Tests\OpenCloudTestCase
{
    public $id;
    public static $json_name = 'ignore';
    public static $url_resource = 'ignore';
    protected $createKeys = array('id');

    public function createJson()
    {
        return parent::createJson();
    }

    public function updateJson($params = array())
    {
        return parent::updateJson($params);
    }
}

class LoadBalancerTest extends \OpenCloud\Tests\OpenCloudTestCase
{

    private $service;
    private $loadBalancer;

    public function __construct()
    {
        $this->service = $this->getClient()->loadBalancerService('cloudLoadBalancers', 'DFW', 'publicURL');
        $this->loadBalancer = $this->service->loadBalancer();
    }

    /**
     * @expectedException OpenCloud\Common\Exceptions\DomainError
     */
    public function test_Add_Node()
    {
        $lb = $this->service->LoadBalancer();
        $lb->addNode('1.1.1.1', 80);
        
        $this->assertEquals('1.1.1.1', $lb->nodes[0]->address);
        
        // this should trigger an error
        $lb->AddNode('1.1.1.2', 80, 'foobar');
    }

    public function test_Remove_Node()
    {
        $lb = $this->service->LoadBalancer();
        
        $lb->addNode('1.1.1.1', 80);
        $lb->addNodes();
        
        $lb->removeNode(1040);
    }

    /**
     * @expectedException OpenCloud\Common\Exceptions\MissingValueError
     */
    public function test_Adding_Nodes_Fails_When_Empty()
    {
        $this->service->loadBalancer()->addNodes();
    }
    
    /**
     * @ expectedException OpenCloud\Common\Exceptions\DomainError
     */
    public function testAddVirtualIp()
    {
        $lb = $this->service->loadBalancer();
        $lb->addVirtualIp('public');
        $this->assertEquals('PUBLIC', $lb->virtualIps[0]->type);
    }

    public function testNode()
    {
        $lb = $this->service->loadBalancer();
        $lb->Create();
        
        $this->assertEquals(
            'https://dfw.loadbalancers.api.rackspacecloud.com/v1.0/TENANT-ID/loadbalancers/123/nodes/321', 
            (string) $lb->Node('321')->Url()
        );
        
        $this->assertInstanceOf(
            'OpenCloud\LoadBalancer\Resource\LoadBalancer', 
            $lb->Node('345')->getParent()
        );
        
        $this->assertEquals(
            'OpenCloud\LoadBalancer\Resource\Node[456]', 
            $lb->Node('456')->Name()
        );
        
        $this->assertInstanceOf(
            'OpenCloud\LoadBalancer\Resource\Metadata', 
            $lb->Node('456')->Metadata()
        );
        
        $this->assertInstanceOf(
            'OpenCloud\Common\Collection', 
            $lb->Node('456')->MetadataList()
        );
        
        $this->assertEquals(
            'https://dfw.loadbalancers.api.rackspacecloud.com/v1.0/TENANT-ID/loadbalancers/123/nodes/456',
            (string) $lb->Node('456')->Url()
        );
    }

    public function testNodeList()
    {
        $lb = $this->service->LoadBalancer();
        $lb->addVirtualIp('PUBLIC', 4);
        $lb->addNode('0.0.0.1', 1000);
        $lb->create(array('name' => 'foobar'));
        $this->assertInstanceOf('OpenCloud\Common\Collection', $lb->NodeList());
    }

    public function testNodeEvent()
    {
        $lb = $this->service->LoadBalancer();
        $lb->Create();
        $this->assertEquals(
            'https://dfw.loadbalancers.api.rackspacecloud.com/v1.0/TENANT-ID/loadbalancers/123/nodes/events',
            (string) $lb->NodeEvent()->Url()
        );
    }

    public function testNodeEventList()
    {
        $lb = $this->service->LoadBalancer();
        $lb->Create();
        $this->assertInstanceOf('OpenCloud\Common\Collection', $lb->NodeEventList());
    }

    public function testVirtualIp()
    {
        $lb = $this->service->LoadBalancer();
        $lb->Create();
        $this->assertEquals(
            'https://dfw.loadbalancers.api.rackspacecloud.com/v1.0/TENANT-ID/loadbalancers/123/virtualips',
            (string) $lb->VirtualIp()->Url()
        );
    }

    public function testVirtualIpList()
    {
        $lb = $this->service->LoadBalancer();
        $lb->Create();
        $this->assertInstanceOf('OpenCloud\Common\Collection', $lb->virtualIpList());
    }

    public function testSessionPersistence()
    {
        $lb = $this->service->LoadBalancer();
        $lb->Create();
        $this->assertEquals(
            'https://dfw.loadbalancers.api.rackspacecloud.com/v1.0/TENANT-ID/loadbalancers/123/sessionpersistence',
            (string) $lb->SessionPersistence()->Url()
        );
    }

    public function testErrorPage()
    {
        $lb = $this->service->LoadBalancer();
        $lb->Create();
        $this->assertEquals(
            'https://dfw.loadbalancers.api.rackspacecloud.com/v1.0/TENANT-ID/' .
            'loadbalancers/123/errorpage',
            (string) $lb->ErrorPage()->Url()
        );
    }

    public function testHealthMonitor()
    {
        $lb = $this->service->LoadBalancer();
        $lb->Create();
        $this->assertEquals(
            'https://dfw.loadbalancers.api.rackspacecloud.com/v1.0/TENANT-ID/loadbalancers/123/healthmonitor',
            (string) $lb->HealthMonitor()->Url()
        );
    }

    public function testStats()
    {
        $this->loadBalancer->id = 1024;

        $x = $this->loadBalancer->stats();
        $this->assertInstanceOf('OpenCloud\LoadBalancer\Resource\Stats', $x);
    }

    public function testUsage()
    {
        $lb = $this->service->LoadBalancer();
        $lb->Create();
        $this->assertEquals(
            'https://dfw.loadbalancers.api.rackspacecloud.com/v1.0/TENANT-ID/' .
            'loadbalancers/123/usage',
            (string) $lb->Usage()->Url()
        );
    }

    public function testAccess()
    {
        $lb = $this->service->LoadBalancer();
        $lb->Create();
        $this->assertEquals(
            'https://dfw.loadbalancers.api.rackspacecloud.com/v1.0/TENANT-ID/loadbalancers/123/accesslist',
            (string) $lb->Access()->Url()
        );
    }

    public function testAccessList()
    {
        $lb = $this->service->LoadBalancer();
        $lb->Create();
        $this->assertInstanceOf(
            'OpenCloud\Common\Collection', 
            $lb->AccessList()
        );
    }

    public function testConnectionThrottle()
    {
        $lb = $this->service->LoadBalancer();
        $lb->Create();
        $this->assertEquals(
            'https://dfw.loadbalancers.api.rackspacecloud.com/v1.0/TENANT-ID/loadbalancers/123/connectionthrottle',
            (string) $lb->ConnectionThrottle()->Url()
        );
    }

    public function testConnectionLogging()
    {
        $lb = $this->service->LoadBalancer();
        $lb->Create();
        $this->assertEquals(
            'https://dfw.loadbalancers.api.rackspacecloud.com/v1.0/TENANT-ID/loadbalancers/123/connectionlogging',
            (string) $lb->ConnectionLogging()->Url()
        );
    }

    public function testContentCaching()
    {
        $lb = $this->service->LoadBalancer();
        $lb->Create();
        $this->assertEquals(
            'https://dfw.loadbalancers.api.rackspacecloud.com/v1.0/TENANT-ID/loadbalancers/123/contentcaching',
            (string) $lb->ContentCaching()->Url()
        );
    }

    public function testSSLTermination()
    {
        $lb = $this->service->LoadBalancer();
        $lb->Create();
        $this->assertEquals(
            'https://dfw.loadbalancers.api.rackspacecloud.com/v1.0/TENANT-ID/loadbalancers/123/ssltermination',
            (string) $lb->SSLTermination()->Url()
        );
    }

    public function testMetadata()
    {
        $lb = $this->service->LoadBalancer();
        $lb->Create();
        $this->assertEquals(
            'https://dfw.loadbalancers.api.rackspacecloud.com/v1.0/TENANT-ID/loadbalancers/123/metadata',
            (string) $lb->Metadata()->Url()
        );
    }

    public function testMetadataList()
    {
        $lb = $this->service->LoadBalancer();
        $lb->Create();
        $this->assertInstanceOf(
            'OpenCloud\Common\Collection', 
            $lb->MetadataList()
        );
    }

    public function testUpdate()
    {
        $lb = $this->service->LoadBalancer();
        $lb->Create();

        $resp = $lb->Update(array(
            'algorithm' => 'ROUND_ROBIN',
            'protocol' => 'HTTP',
            'port' => '8080'
        ));

        $this->assertNotNull($resp->getStatusCode());

        $this->assertEquals('ROUND_ROBIN',$lb->algorithm);
        $this->assertEquals('HTTP',$lb->protocol);
        $this->assertEquals('8080',$lb->port);
    }
    
    /**
     * @expectedException OpenCloud\Common\Exceptions\InvalidArgumentError
     */
    public function test_Update_Fails_Without_Correct_Fields()
    {
        $this->loadBalancer->update(array('foo' => 'bar'));
    }
    
    public function testAddingNodeWithType()
    {
        $this->loadBalancer->addNode('localhost', 8080, 'ENABLED', 'PRIMARY', 10);
    }
    
    /**
     * @expectedException OpenCloud\Common\Exceptions\DomainError
     */
    public function testAddingNodeFailsWithoutCorrectType()
    {
        $this->loadBalancer->addNode('localhost', 8080, 'ENABLED', 'foo');
    }
    
    /**
     * @expectedException OpenCloud\Common\Exceptions\DomainError
     */
    public function testAddingNodeFailsWithoutCorrectWeight()
    {
        $this->loadBalancer->addNode('localhost', 8080, 'ENABLED', 'PRIMARY', 'baz');
    }
    
    public function testAddingVirtualIp()
    {
        $this->loadBalancer->id = 2000;
        $this->loadBalancer->addVirtualIp(123, 4);
        $this->loadBalancer->addVirtualIp('PUBLIC', 6);
    }
    
    /**
     * @expectedException OpenCloud\Common\Exceptions\DomainError
     */
    public function testAddingVirtualIpFailsWithIncorrectIpType()
    {
        $this->loadBalancer->addVirtualIp(123, 5);
    } 
    
}
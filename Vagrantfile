# -*- mode: ruby -*-
# vi: set ft=ruby :

# All Vagrant configuration is done below. The "2" in Vagrant.configure
# configures the configuration version (we support older styles for
# backwards compatibility). Please don't change it unless you know what
# you're doing.
Vagrant.configure(2) do |config|
    config.vm.define "mc" do |mc|
        mc.vm.box_url = "https://cloud-images.ubuntu.com/vagrant/precise/current/precise-server-cloudimg-i386-vagrant-disk1.box"
        mc.vm.box = "mc"
        mc.vm.synced_folder ".", "/home/vagrant/web", type: "nfs"
        mc.vm.hostname = "mathtrade.local"
        mc.vm.network "private_network", ip: "172.21.88.2"
        mc.vm.network :forwarded_port, guest: 22, host:3333
        mc.vm.provider :virtualbox do |vb|
            vb.memory = 1024
        end
        mc.vm.provision "ansible" do |ansible|
            ansible.playbook = "docs/environment/vagrant/playbook.yml"
        end
    end
end

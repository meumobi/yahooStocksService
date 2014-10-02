def absolute_writable_paths
  linked_dirs = fetch(:linked_dirs)
  fetch(:file_permissions_paths).map do |d|
    Array(linked_dirs).include?(d) ? shared_path.join(d) : release_path.join(d)
  end
end

namespace :deploy do
  namespace :permissions do
    desc "Recursively set mode (from \"file_permissions_chmod_mode\") on configured paths with chmod"
    task :chmod => [:check] do
      next unless any? :file_permissions_paths
      on roles fetch(:file_permissions_roles) do |host|
        execute :chmod, fetch(:file_permissions_chmod_mode), *absolute_writable_paths
      end
    end
  end
end


namespace :load do
  task :defaults do
    set :file_permissions_roles, :all
    set :file_permissions_paths, []
    set :file_permissions_users, []
    set :file_permissions_groups, []
    set :file_permissions_chmod_mode, "0777"
  end
end

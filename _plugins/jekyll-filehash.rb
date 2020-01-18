# require 'digest/md5'

# module Jekyll
#   class HashFile < Liquid::Tag

#     def initialize(tag_name, file, tokens)
#       super
#       filepath = File.join(File.dirname(__FILE__), '..', file)
#       @filepath = filepath.strip
#     end

#     def render(context)
#       Digest::MD5.hexdigest IO.read File.open("#{@filepath}")
#     end
#   end
# end

# Liquid::Template.register_tag('filehash', Jekyll::HashFile)
